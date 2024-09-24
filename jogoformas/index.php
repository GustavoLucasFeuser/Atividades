<?php
// Configurações de conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "geometria";
require_once 'Quadrado.php';
require_once 'Circulo.php';
require_once 'Triangulo.php'; // Incluindo o arquivo Triangulo.php

// Cria a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verifica se é uma exclusão
    if (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $sql = "DELETE FROM forma WHERE id = '$id'";
        if ($conn->query($sql) === TRUE) {
            $message = "Forma excluída com sucesso!";
        } else {
            $message = "Erro ao excluir forma: " . $conn->error;
        }
    } else {
        // Trata inserção/atualização
        $tipo = isset($_POST['tipo']) ? $_POST['tipo'] : '';
        $cor = isset($_POST['cor']) ? $_POST['cor'] : '';
        $unidade_medida_id = isset($_POST['unidade_medida_id']) ? $_POST['unidade_medida_id'] : '';

        if ($tipo == 'quadrado') {
            $lado = isset($_POST['lado']) ? $_POST['lado'] : '';

            if (isset($_POST['update'])) {
                // Atualiza quadrado existente
                $id = $_POST['id'];
                $message = editarQuadrado($conn, $id, $lado, $cor, $unidade_medida_id);
            } else {
                // Insere novo quadrado
                $message = criarQuadrado($conn, $lado, $cor, $unidade_medida_id);
            }
        } elseif ($tipo == 'circulo') {
            $raio = isset($_POST['raio']) ? $_POST['raio'] : '';
            $circulo = new Circulo(null, $raio, $cor);
            $circulo->unidade_medida_id = $unidade_medida_id; // Supondo que este atributo exista

            if (isset($_POST['update'])) {
                $id = $_POST['id'];
                $circulo->setId($id);
                $message = $circulo->editar($conn);
            } else {
                $message = $circulo->criar($conn);
            }
        } elseif ($tipo == 'triangulo') {
            $base = isset($_POST['base']) ? $_POST['base'] : '';
            $altura = isset($_POST['altura']) ? $_POST['altura'] : '';
            $triangulo = new Triangulo(null, $base, $altura, $cor);
            $triangulo->unidade_medida_id = $unidade_medida_id; // Supondo que este atributo exista

            if (isset($_POST['update'])) {
                $id = $_POST['id'];
                $triangulo->setId($id);
                $message = $triangulo->editar($conn);
            } else {
                $message = $triangulo->criar($conn);
            }
        }
    }
}

// Pesquisa
$search_query = "";
if (isset($_POST['search'])) {
    $search_tipo = isset($_POST['search_tipo']) ? $_POST['search_tipo'] : '';
    $search_cor = isset($_POST['search_cor']) ? $_POST['search_cor'] : '';
    $search_lado = isset($_POST['search_lado']) ? $_POST['search_lado'] : '';
    $search_raio = isset($_POST['search_raio']) ? $_POST['search_raio'] : '';
    $search_base = isset($_POST['search_base']) ? $_POST['search_base'] : '';
    $search_altura = isset($_POST['search_altura']) ? $_POST['search_altura'] : '';

    $search_query = " WHERE 1=1";
    if ($search_tipo) {
        $search_query .= " AND tipo = '$search_tipo'";
    }
    if ($search_cor) {
        $search_cor_hex = $conn->real_escape_string($search_cor);
        $search_query .= " AND cor = '$search_cor_hex'";
    }
    if ($search_lado) {
        $search_query .= " AND lado LIKE '%$search_lado%'";
    }
    if ($search_raio) {
        $search_query .= " AND raio LIKE '%$search_raio%'";
    }
    if ($search_base) {
        $search_query .= " AND base LIKE '%$search_base%'";
    }
    if ($search_altura) {
        $search_query .= " AND altura LIKE '%$search_altura%'";
    }
}

// Obtém todas as formas do banco de dados
$formas = $conn->query("SELECT f.*, u.nome as unidade_nome, u.simbolo as unidade_simbolo FROM forma f LEFT JOIN unidade_medida u ON f.unidade_medida_id = u.id $search_query ORDER BY f.tipo, IF(f.tipo = 'quadrado', f.lado, IF(f.tipo = 'circulo', f.raio, f.base)) DESC");

?>

<!DOCTYPE html>
<html>
<head>
    <title>CRUD de Formas com Unidade de Medida</title>
    <style>
        .forma {
            display: inline-block;
            margin: 10px;
            text-align: center;
            vertical-align: middle;
            color: black;
            position: relative;
        }

        .forma span {
            display: block;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }
    </style>
</head>
<body>
    <h1>CRUD de Formas com Unidade de Medida</h1>
    <a href="unidade_medida.php">
        <input type="submit" value="Cadastro de UM">
    </a>
    
    <?php if ($message): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="post" action="">
        <input type="hidden" name="id" id="id">
        <label for="tipo">Tipo:</label>
        <select name="tipo" id="tipo" onchange="toggleFields()" required>
            <option value="">Selecione</option>
            <option value="quadrado">Quadrado</option>
            <option value="circulo">Círculo</option>
            <option value="triangulo">Triângulo</option>
        </select>

        <label for="unidade_medida_id">Unidade de Medida:</label>
        <select name="unidade_medida_id" id="unidade_medida_id" required>
            <?php
            // Obtém unidades de medida do banco de dados
            $unidades = $conn->query("SELECT * FROM unidade_medida");
            while ($unidade = $unidades->fetch_assoc()):
            ?>
                <option value="<?php echo $unidade['id']; ?>"><?php echo $unidade['nome']; ?> (<?php echo $unidade['simbolo']; ?>)</option>
            <?php endwhile; ?>
        </select>

        <div id="quadrado-fields" style="display: none;">
            <label for="lado">Lado:</label>
            <input type="text" name="lado" id="lado">
        </div>

        <div id="circulo-fields" style="display: none;">
            <label for="raio">Raio:</label>
            <input type="text" name="raio" id="raio">
        </div>

        <div id="triangulo-fields" style="display: none;">
            <label for="base">Base:</label>
            <input type="text" name="base" id="base">
            
            <label for="altura">Altura:</label>
            <input type="text" name="altura" id="altura">
        </div>

        <label for="cor">Cor:</label>
        <input type="color" name="cor" id="cor" required>
        <button type="submit" name="create">Cadastrar</button>
        <button type="submit" name="update">Atualizar</button>
    </form>

    <h2>Listagem de Formas</h2>

    <form method="post">
        <h3>Pesquisar Formas</h3>
        <label for="search_tipo">Tipo:</label>
        <select name="search_tipo" id="search_tipo">
            <option value="">Todos</option>
            <option value="quadrado">Quadrado</option>
            <option value="circulo">Círculo</option>
            <option value="triangulo">Triângulo</option>
        </select>

        <label for="search_cor">Cor:</label>
        <input type="text" name="search_cor" id="search_cor" placeholder="#RRGGBB">

        <label for="search_lado">Lado:</label>
        <input type="text" name="search_lado" id="search_lado">

        <label for="search_raio">Raio:</label>
        <input type="text" name="search_raio" id="search_raio">

        <label for="search_base">Base:</label>
        <input type="text" name="search_base" id="search_base">

        <label for="search_altura">Altura:</label>
        <input type="text" name="search_altura" id="search_altura">

        <button type="submit" name="search">Buscar</button>
    </form>

    <div class="formas">
        <?php while ($forma = $formas->fetch_assoc()): ?>
            <div class="forma" style="border: 1px solid black;">
                <?php if ($forma['tipo'] == 'quadrado'): ?>
                    <div style="width: <?php echo $forma['lado'] . $forma['unidade_simbolo']; ?>; height: <?php echo $forma['lado'] . $forma['unidade_simbolo']; ?>; background-color: <?php echo $forma['cor']; ?>;">
                        <span><?php echo $forma['lado'] . ' ' . $forma['unidade_nome']; ?></span>
                    </div>
                <?php elseif ($forma['tipo'] == 'circulo'): ?>
                    <div style="width: <?php echo $forma['raio'] * 2 . $forma['unidade_simbolo']; ?>; height: <?php echo $forma['raio'] * 2 . $forma['unidade_simbolo']; ?>; border-radius: 50%; background-color: <?php echo $forma['cor']; ?>;">
                        <span><?php echo $forma['raio'] . ' ' . $forma['unidade_nome']; ?></span>
                    </div>
                <?php elseif ($forma['tipo'] == 'triangulo'): ?>
                    <div style="width: 0; height: 0; border-left: <?php echo $forma['base'] / 2 . $forma['unidade_simbolo']; ?> solid transparent; border-right: <?php echo $forma['base'] / 2 . $forma['unidade_simbolo']; ?> solid transparent; border-bottom: <?php echo $forma['altura'] . $forma['unidade_simbolo']; ?> solid <?php echo $forma['cor']; ?>;">
                        <span><?php echo 'Base: ' . $forma['base'] . ' ' . $forma['unidade_nome'] . ', Altura: ' . $forma['altura'] . ' ' . $forma['unidade_nome']; ?></span>
                    </div>
                <?php endif; ?>
                
                <form method="post" action="">
                    <input type="hidden" name="id" value="<?php echo $forma['id']; ?>">
                    <button type="submit" name="delete">Excluir</button>
                    <button type="button" onclick="editForma(<?php echo $forma['id']; ?>, '<?php echo $forma['tipo']; ?>', '<?php echo $forma['cor']; ?>', '<?php echo $forma['lado'] ?? ''; ?>', '<?php echo $forma['raio'] ?? ''; ?>', '<?php echo $forma['base'] ?? ''; ?>', '<?php echo $forma['altura'] ?? ''; ?>', '<?php echo $forma['unidade_medida_id']; ?>')">Editar</button>
                </form>
            </div>
        <?php endwhile; ?>
    </div>

    <script>
        function toggleFields() {
            var tipo = document.getElementById('tipo').value;
            document.getElementById('quadrado-fields').style.display = tipo === 'quadrado' ? 'block' : 'none';
            document.getElementById('circulo-fields').style.display = tipo === 'circulo' ? 'block' : 'none';
            document.getElementById('triangulo-fields').style.display = tipo === 'triangulo' ? 'block' : 'none';
        }

        function editForma(id, tipo, cor, lado, raio, base, altura, unidade_medida_id) {
            document.getElementById('id').value = id;
            document.getElementById('tipo').value = tipo;
            document.getElementById('cor').value = cor;
            document.getElementById('unidade_medida_id').value = unidade_medida_id;

            if (tipo === 'quadrado') {
                document.getElementById('lado').value = lado;
                toggleFields();
            } else if (tipo === 'circulo') {
                document.getElementById('raio').value = raio;
                toggleFields();
            } else if (tipo === 'triangulo') {
                document.getElementById('base').value = base;
                document.getElementById('altura').value = altura;
                toggleFields();
            }
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>
