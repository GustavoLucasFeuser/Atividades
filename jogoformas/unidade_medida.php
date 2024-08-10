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

// Funções CRUD para Unidades de Medida
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $simbolo = $_POST['simbolo'];

    if (isset($_POST['create'])) {
        // Cria uma nova unidade de medida
        $sql = "INSERT INTO unidade_medida (nome, simbolo) VALUES ('$nome', '$simbolo')";
        $conn->query($sql);
    } elseif (isset($_POST['update'])) {
        $id = $_POST['id'];
        // Atualiza uma unidade de medida existente
        $sql = "UPDATE unidade_medida SET nome='$nome', simbolo='$simbolo' WHERE id=$id";
        $conn->query($sql);
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];
        // Exclui uma unidade de medida
        $sql = "DELETE FROM unidade_medida WHERE id=$id";
        $conn->query($sql);
    }
}

// Obtém todas as unidades de medida do banco de dados
$unidades_medida = $conn->query("SELECT * FROM unidade_medida");

?>

<!DOCTYPE html>
<html>
<head>
    <title>CRUD de Unidades de Medida</title>
</head>
<body>
    <h1>CRUD de Unidades de Medida</h1>
    <form method="post">
        <!-- Campo oculto para armazenar o ID da unidade de medida -->
        <input type="hidden" name="id" id="id">
        <!-- Campo para o nome da unidade -->
        <label for="nome">Nome:</label>
        <input type="text" name="nome" id="nome" required>
        <!-- Campo para o símbolo da unidade -->
        <label for="simbolo">Símbolo:</label>
        <input type="text" name="simbolo" id="simbolo" required>
        <!-- Botões para criar, atualizar e excluir unidades de medida -->
        <button type="submit" name="create">Cadastrar</button>
        <button type="submit" name="update">Atualizar</button>
        <button type="submit" name="delete">Excluir</button>
    </form>

    <h2>Listagem de Unidades de Medida</h2>
    <?php if ($unidades_medida->num_rows > 0): ?>
        <ul>
            <?php while($row = $unidades_medida->fetch_assoc()): ?>
                <li>
                    ID: <?php echo $row['id']; ?>,
                    Nome: <?php echo $row['nome']; ?>,
                    Símbolo: <?php echo $row['simbolo']; ?>
                    <button onclick="editUnidade('<?php echo $row['id']; ?>', '<?php echo $row['nome']; ?>', '<?php echo $row['simbolo']; ?>')">Editar</button>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                        <button type="submit" name="delete">Excluir</button>
                    </form>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>Nenhuma unidade de medida cadastrada.</p>
    <?php endif; ?>

    <script>
        function editUnidade(id, nome, simbolo) {
            document.getElementById("id").value = id;
            document.getElementById("nome").value = nome;
            document.getElementById("simbolo").value = simbolo;
        }
    </script>
</body>
</html>
