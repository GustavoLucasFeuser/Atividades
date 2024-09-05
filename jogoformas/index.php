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
$formData = [
    'id' => '',
    'tipo' => '',
    'lado' => '',
    'raio' => '',
    'lado1' => '',
    'lado2' => '',
    'lado3' => '',
    'cor' => '',
    'unidade_medida_id' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $sql = "DELETE FROM forma WHERE id = '$id'";
        if ($conn->query($sql) === TRUE) {
            $message = "Forma excluída com sucesso!";
        } else {
            $message = "Erro ao excluir forma: " . $conn->error;
        }
    } elseif (isset($_POST['edit'])) {
        $id = $_POST['id'];
        // Busca os dados da forma para preencher o formulário
        $sql = "SELECT * FROM forma WHERE id = '$id'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $formData = $result->fetch_assoc();
        }
    } else {
        $tipo = isset($_POST['tipo']) ? $_POST['tipo'] : '';
        $cor = isset($_POST['cor']) ? $_POST['cor'] : '';
        $unidade_medida_id = isset($_POST['unidade_medida_id']) ? $_POST['unidade_medida_id'] : '';

        if ($tipo == 'quadrado') {
            $lado = isset($_POST['lado']) ? $_POST['lado'] : '';

            if (isset($_POST['update'])) {
                $id = $_POST['id'];
                $message = editarQuadrado($conn, $id, $lado, $cor, $unidade_medida_id);
            } else {
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
        <input type="hidden" name="id" id="id" value="<?php echo htmlspecialchars($formData['id']); ?>">
        <label for="tipo">Tipo:</label>
        <select name="tipo" id="tipo" onchange="toggleFields()" required>
            <option value="">Selecione</option>
            <option value="quadrado" <?php if ($formData['tipo'] == 'quadrado') echo 'selected'; ?>>Quadrado</option>
            <option value="circulo" <?php if ($formData['tipo'] == 'circulo') echo 'selected'; ?>>Círculo</option>
            <option value="triangulo" <?php if ($formData['tipo'] == 'triangulo') echo 'selected'; ?>>Triângulo</option>
        </select>

        <label for="unidade_medida_id">Unidade de Medida:</label>
        <select name="unidade_medida_id" id="unidade_medida_id" required>
            <?php
            // Obtém unidades de medida do banco de dados
            $unidades = $conn->query("SELECT * FROM unidade_medida");
            while ($unidade = $unidades->fetch_assoc()):
            ?>
                <option value="<?php echo $unidade['id']; ?>" <?php if ($formData['unidade_medida_id'] == $unidade['id']) echo 'selected'; ?>><?php echo $unidade['nome']; ?> (<?php echo $unidade['simbolo']; ?>)</option>
            <?php endwhile; ?>
        </select>

        <div id="quadrado-fields" style="display: <?php echo $formData['tipo'] == 'quadrado' ? 'block' : 'none'; ?>;">
            <label for="lado">Lado:</label>
            <input type="text" name="lado" id="lado" value="<?php echo htmlspecialchars($formData['lado']); ?>">
        </div>

        <div id="circulo-fields" style="display: <?php echo $formData['tipo'] == 'circulo' ? 'block' : 'none'; ?>;">
            <label for="raio">Raio:</label>
            <input type="text" name="raio" id="raio" value="<?php echo htmlspecialchars($formData['raio']); ?>">
        </div>

        <div id="triangulo-fields" style="display: <?php echo $formData['tipo'] == 'triangulo' ? 'block' : 'none'; ?>;">
            <label for="lado1">Lado 1:</label>
            <input type="text" name="lado1" id="lado1" value="<?php echo htmlspecialchars($formData['lado1']); ?>">
            <label for="lado2">Lado 2:</label>
            <input type="text" name="lado2" id="lado2" value="<?php echo htmlspecialchars($formData['lado2']); ?>">
            <label for="lado3">Lado 3:</label>
            <input type="text" name="lado3" id="lado3" value="<?php echo htmlspecialchars($formData['lado3']); ?>">
        </div>

        <label for="cor">Cor (em hexadecimal):</label>
        <input type="text" name="cor" id="cor" value="<?php echo htmlspecialchars($formData['cor']); ?>">

        <?php if ($formData['id']): ?>
            <button type="submit" name="update">Atualizar</button>
        <?php else: ?>
            <button type="submit" name="create">Criar</button>
        <?php endif; ?>
        <button type="submit" name="delete">Excluir</button>
    </form>

    <form method="post" action="">
        <label for="search_tipo">Tipo:</label>
        <select name="search_tipo" id="search_tipo">
            <option value="">Todos</option>
            <option value="quadrado" <?php if (isset($_POST['search_tipo']) && $_POST['search_tipo'] == 'quadrado') echo 'selected'; ?>>Quadrado</option>
            <option value="circulo" <?php if (isset($_POST['search_tipo']) && $_POST['search_tipo'] == 'circulo') echo 'selected'; ?>>Círculo</option>
            <option value="triangulo" <?php if (isset($_POST['search_tipo']) && $_POST['search_tipo'] == 'triangulo') echo 'selected'; ?>>Triângulo</option>
        </select>

        <label for="search_cor">Cor (em hexadecimal):</label>
        <input type="text" name="search_cor" id="search_cor" value="<?php if (isset($_POST['search_cor'])) echo htmlspecialchars($_POST['search_cor']); ?>">

        <label for="search_lado">Lado:</label>
        <input type="text" name="search_lado" id="search_lado" value="<?php if (isset($_POST['search_lado'])) echo htmlspecialchars($_POST['search_lado']); ?>">

        <label for="search_raio">Raio:</label>
        <input type="text" name="search_raio" id="search_raio" value="<?php if (isset($_POST['search_raio'])) echo htmlspecialchars($_POST['search_raio']); ?>">

        <button type="submit" name="search">Pesquisar</button>
    </form>

    <h2>Formas Cadastradas</h2>
    <?php if ($formas->num_rows > 0): ?>
        <div>
            <?php while ($forma = $formas->fetch_assoc()): ?>
                <div class="forma" style="background-color: <?php echo htmlspecialchars($forma['cor']); ?>;">
                    <span>
                        <strong>Tipo:</strong> <?php echo htmlspecialchars($forma['tipo']); ?><br>
                        <?php if ($forma['tipo'] == 'quadrado'): ?>
                            <strong>Lado:</strong> <?php echo htmlspecialchars($forma['lado']); ?><br>
                        <?php elseif ($forma['tipo'] == 'circulo'): ?>
                            <strong>Raio:</strong> <?php echo htmlspecialchars($forma['raio']); ?><br>
                        <?php elseif ($forma['tipo'] == 'triangulo'): ?>
                            <strong>Lado 1:</strong> <?php echo htmlspecialchars($forma['lado1']); ?><br>
                            <strong>Lado 2:</strong> <?php echo htmlspecialchars($forma['lado2']); ?><br>
                            <strong>Lado 3:</strong> <?php echo htmlspecialchars($forma['lado3']); ?><br>
                        <?php endif; ?>
                        <strong>Cor:</strong> <?php echo htmlspecialchars($forma['cor']); ?><br>
                        <strong>Unidade:</strong> <?php echo htmlspecialchars($forma['unidade_nome']); ?> (<?php echo htmlspecialchars($forma['unidade_simbolo']); ?>)<br>
                        <form method="post" action="">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($forma['id']); ?>">
                            <input type="hidden" name="tipo" value="<?php echo htmlspecialchars($forma['tipo']); ?>">
                            <button type="submit" name="edit">Editar</button>
                        </form>
                    </span>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p>Nenhuma forma encontrada.</p>
    <?php endif; ?>

    <script>
        function toggleFields() {
            var tipo = document.getElementById('tipo').value;
            document.getElementById('quadrado-fields').style.display = tipo === 'quadrado' ? 'block' : 'none';
            document.getElementById('circulo-fields').style.display = tipo === 'circulo' ? 'block' : 'none';
            document.getElementById('triangulo-fields').style.display = tipo === 'triangulo' ? 'block' : 'none';
        }

        // Se o formulário está para edição, mostra os campos apropriados
        document.addEventListener('DOMContentLoaded', function () {
            var tipo = document.getElementById('tipo').value;
            if (tipo) {
                toggleFields();
            }
        });
    </script>
</body>
</html>

<?php $conn->close(); ?>