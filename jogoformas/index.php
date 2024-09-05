<?php
// Configurações de conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "geometria";
require_once 'Quadrado.php';
require_once 'Circulo.php';
require_once 'Triangulo.php';

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

            if (isset($_POST['update'])) {
                $id = $_POST['id'];
                $circulo = new Circulo($id, $raio, $cor);
                $circulo->unidade_medida_id = $unidade_medida_id;
                $message = $circulo->editar($conn);
            } else {
                $circulo = new Circulo(null, $raio, $cor);
                $circulo->unidade_medida_id = $unidade_medida_id;
                $message = $circulo->criar($conn);
            }
        } elseif ($tipo == 'triangulo') {
            $lado1 = isset($_POST['lado1']) ? $_POST['lado1'] : '';
            $lado2 = isset($_POST['lado2']) ? $_POST['lado2'] : '';
            $lado3 = isset($_POST['lado3']) ? $_POST['lado3'] : '';

            if (isset($_POST['update'])) {
                $id = $_POST['id'];
                $triangulo = new Triangulo($id, $lado1, $lado2, $lado3, $cor);
                $triangulo->unidade_medida_id = $unidade_medida_id;
                $message = $triangulo->editar($conn);
            } else {
                $triangulo = new Triangulo(null, $lado1, $lado2, $lado3, $cor);
                $triangulo->unidade_medida_id = $unidade_medida_id;
                $message = $triangulo->criar($conn, $lado1, $lado2, $lado3);
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
}

// Obtém todas as formas do banco de dados
$formas = $conn->query("SELECT f.*, u.nome as unidade_nome, u.simbolo as unidade_simbolo FROM forma f LEFT JOIN unidade_medida u ON f.unidade_medida_id = u.id $search_query ORDER BY f.tipo, IF(f.tipo = 'quadrado', f.lado, f.raio) DESC");
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
            <label for="lado1">Lado 1:</label>
            <input type="text" name="lado1" id="lado1">
            <label for="lado2">Lado 2:</label>
            <input type="text" name="lado2" id="lado2">
            <label for="lado3">Lado 3:</label>
            <input type="text" name="lado3" id="lado3">
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
        <input type="color" name="search_cor" id="search_cor">

        <label for="search_lado">Lado (Quadrado):</label>
        <input type="text" name="search_lado" id="search_lado">

        <label for="search_raio">Raio (Círculo):</label>
        <input type="text" name="search_raio" id="search_raio">

        <button type="submit" name="search">Pesquisar</button>
    </form>

    <div id="formas-list">
        <?php while ($forma = $formas->fetch_assoc()): ?>
            <div class="forma">
                <?php if ($forma['tipo'] == 'quadrado'): ?>
                    <div style="width: <?php echo $forma['lado']; ?>px; height: <?php echo $forma['lado']; ?>px; background-color: <?php echo $forma['cor']; ?>;"></div>
                <?php elseif ($forma['tipo'] == 'circulo'): ?>
                    <div style="width: <?php echo $forma['raio']*2; ?>px; height: <?php echo $forma['raio']*2; ?>px; background-color: <?php echo $forma['cor']; ?>; border-radius: 50%;"></div>
                <?php elseif ($forma['tipo'] == 'triangulo'): ?>
                    <div style="width: 0; height: 0; border-left: <?php echo $forma['lado1']; ?>px solid transparent; border-right: <?php echo $forma['lado2']; ?>px solid transparent; border-bottom: <?php echo $forma['lado3']; ?>px solid <?php echo $forma['cor']; ?>;"></div>
                <?php endif; ?>
                <span><?php echo ucfirst($forma['tipo']); ?></span>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="id" value="<?php echo $forma['id']; ?>">
                    <button type="submit" name="delete">Excluir</button>
                </form>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="id" value="<?php echo $forma['id']; ?>">
                    <button type="submit" name="edit">Editar</button>
                </form>
            </div>
        <?php endwhile; ?>
    </div>

    <script>
        function toggleFields() {
            var tipo = document.getElementById('tipo').value;
            document.getElementById('quadrado-fields').style.display = (tipo === 'quadrado') ? 'block' : 'none';
            document.getElementById('circulo-fields').style.display = (tipo === 'circulo') ? 'block' : 'none';
            document.getElementById('triangulo-fields').style.display = (tipo === 'triangulo') ? 'block' : 'none';
        }
    </script>
</body>
</html>
