<?php
// Configurações de conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "geometria";

// Cria a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

// Funções CRUD para as formas
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verifica se é uma exclusão
    if (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $sql = "DELETE FROM forma WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $id);
        if ($stmt->execute()) {
            $message = "Forma excluída com sucesso!";
        } else {
            $message = "Erro ao excluir forma: " . $conn->error;
        }
    } else {
        // Trata inserção/atualização
        $id = isset($_POST['id']) ? $_POST['id'] : null;
        $tipo = $_POST['tipo'];
        $cor = $_POST['cor'];
        $unidade_medida_id = $_POST['unidade_medida_id'];

        // Valida unidade de medida
        if (!in_array($unidade_medida_id, ['1', '2', '3'])) {
            $message = "Erro: Unidade de medida inválida!";
        } else {
            if ($tipo == 'quadrado') {
                $lado = $_POST['lado'];
                if ($id) {
                    // Atualiza forma existente
                    $sql = "UPDATE forma SET tipo=?, lado=?, cor=?, unidade_medida_id=? WHERE id=?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param('sdsii', $tipo, $lado, $cor, $unidade_medida_id, $id);
                } else {
                    // Insere nova forma
                    $sql = "INSERT INTO forma (tipo, lado, cor, unidade_medida_id) VALUES (?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param('sdsi', $tipo, $lado, $cor, $unidade_medida_id);
                }
            } else {
                $raio = $_POST['raio'];
                if ($id) {
                    // Atualiza forma existente
                    $sql = "UPDATE forma SET tipo=?, raio=?, cor=?, unidade_medida_id=? WHERE id=?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param('sdsii', $tipo, $raio, $cor, $unidade_medida_id, $id);
                } else {
                    // Insere nova forma
                    $sql = "INSERT INTO forma (tipo, raio, cor, unidade_medida_id) VALUES (?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param('sdsi', $tipo, $raio, $cor, $unidade_medida_id);
                }
            }
            if ($stmt->execute()) {
                $message = $id ? "Forma atualizada com sucesso!" : "Forma cadastrada com sucesso!";
            } else {
                $message = "Erro ao salvar forma: " . $conn->error;
            }
        }
    }
}

// Obtém todas as formas do banco de dados
$formas = $conn->query("SELECT f.*, u.nome as unidade_nome, u.simbolo as unidade_simbolo FROM forma f LEFT JOIN unidade_medida u ON f.unidade_medida_id = u.id ORDER BY f.tipo, IF(f.tipo = 'quadrado', f.lado, f.raio) DESC");

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

    <form method="post">
        <input type="hidden" name="id" id="id">
        <label for="tipo">Tipo:</label>
        <select name="tipo" id="tipo" onchange="toggleFields()" required>
            <option value="">Selecione</option>
            <option value="quadrado">Quadrado</option>
            <option value="circulo">Círculo</option>
        </select>

        <label for="unidade_medida_id">Unidade de Medida:</label>
        <select name="unidade_medida_id" id="unidade_medida_id" required>
            <option value="">Selecione</option>
            <option value="1">Pixels (px)</option>
            <option value="2">Milímetros (mm)</option>
            <option value="3">Centímetros (cm)</option>
        </select>

        <div id="quadrado-fields" style="display: none;">
            <label for="lado">Lado:</label>
            <input type="text" name="lado" id="lado">
        </div>

        <div id="circulo-fields" style="display: none;">
            <label for="raio">Raio:</label>
            <input type="text" name="raio" id="raio">
        </div>

        <label for="cor">Cor:</label>
        <input type="color" name="cor" id="cor" required>
        <button type="submit" name="create">Cadastrar</button>
        <button type="submit" name="update" id="update-button" style="display: none;">Atualizar</button>
    </form>

    <h2>Listagem de Formas</h2>
    <?php if ($formas->num_rows > 0): ?>
        <?php while($row = $formas->fetch_assoc()): ?>
            <?php
                // Define o tamanho e estilo com base na unidade de medida
                $tamanho = $row['tipo'] == 'quadrado' ? $row['lado'] : $row['raio'] * 2;
                $unidade = $row['unidade_simbolo'];
                
                // Aplica o estilo com base na unidade de medida
                if ($unidade == '%') {
                    $style = "width: $tamanho; height: $tamanho;";
                } else {
                    $style = $row['tipo'] == 'quadrado'
                        ? "width: {$tamanho}{$unidade}; height: {$tamanho}{$unidade};"
                        : "width: {$tamanho}{$unidade}; height: {$tamanho}{$unidade}; border-radius: 50%;";
                }
            ?>
            <?php if ($row['tipo'] == 'quadrado'): ?>
                <div class="forma" style="background-color: <?php echo $row['cor']; ?>; <?php echo $style; ?>">
                    <span>
                        ID: <?php echo $row['id']; ?><br>
                        Tipo: Quadrado<br>
                        Lado: <?php echo $row['lado']; ?> <?php echo $row['unidade_simbolo']; ?><br>
                        <button onclick="editForma('<?php echo $row['id']; ?>', 'quadrado', '<?php echo $row['lado']; ?>', '', '<?php echo $row['cor']; ?>', '<?php echo $row['unidade_medida_id']; ?>')">Editar</button>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="delete">Excluir</button>
                        </form>
                    </span>
                </div>
            <?php else: ?>
                <div class="forma" style="background-color: <?php echo $row['cor']; ?>; <?php echo $style; ?>">
                    <span>
                        ID: <?php echo $row['id']; ?><br>
                        Tipo: Círculo<br>
                        Raio: <?php echo $row['raio']; ?> <?php echo $row['unidade_simbolo']; ?><br>
                        <button onclick="editForma('<?php echo $row['id']; ?>', 'circulo', '', '<?php echo $row['raio']; ?>', '<?php echo $row['cor']; ?>', '<?php echo $row['unidade_medida_id']; ?>')">Editar</button>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="delete">Excluir</button>
                        </form>
                    </span>
                </div>
            <?php endif; ?>
        <?php endwhile; ?>
    <?php else: ?>
        <p>Nenhuma forma cadastrada.</p>
    <?php endif; ?>

    <script>
        function toggleFields() {
            var tipo = document.getElementById("tipo").value;
            document.getElementById("quadrado-fields").style.display = tipo == "quadrado" ? "block" : "none";
            document.getElementById("circulo-fields").style.display = tipo == "circulo" ? "block" : "none";
            document.getElementById("update-button").style.display = tipo ? "inline" : "none";
        }

        function editForma(id, tipo, lado, raio, cor, unidade_medida_id) {
            document.getElementById("id").value = id;
            document.getElementById("tipo").value = tipo;
            document.getElementById("lado").value = lado;
            document.getElementById("raio").value = raio;
            document.getElementById("cor").value = cor;
            document.getElementById("unidade_medida_id").value = unidade_medida_id;
            toggleFields();
        }
    </script>
</body>
</html>
