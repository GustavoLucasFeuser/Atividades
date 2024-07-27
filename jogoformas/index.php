<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "geometria";

// Cria conexão com o banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Funções CRUD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create'])) {
        $lado = $_POST['lado'];
        $cor = $_POST['cor'];
        $sql = "INSERT INTO quadrado (lado, cor) VALUES ('$lado', '$cor')";
        $conn->query($sql);
    } elseif (isset($_POST['update'])) {
        $id = $_POST['id'];
        $lado = $_POST['lado'];
        $cor = $_POST['cor'];
        $sql = "UPDATE quadrado SET lado='$lado', cor='$cor' WHERE id=$id";
        $conn->query($sql);
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $sql = "DELETE FROM quadrado WHERE id=$id";
        $conn->query($sql);
    }
}

$quadrados = $conn->query("SELECT * FROM quadrado ORDER BY lado DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>CRUD de Quadrados</title>
    <style>
        .quadrado {
            display: inline-block;
            margin: 10px;
            text-align: center;
            vertical-align: middle;
            color: black;
            line-height: 1;
            position: relative;
        }

        .quadrado span {
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
    <h1>CRUD de Quadrados</h1>
    <form method="post">
        <input type="hidden" name="id" id="id">
        <label for="lado">Lado:</label>
        <input type="text" name="lado" id="lado" required>
        <label for="cor">Cor:</label>
        <input type="color" name="cor" id="cor" required>
        <button type="submit" name="create">Cadastrar</button>
        <button type="submit" name="update">Atualizar</button>
        <button type="submit" name="delete">Excluir</button>
    </form>

    <h2>Listagem de Quadrados</h2>
    <?php if ($quadrados->num_rows > 0): ?>
        <?php while($row = $quadrados->fetch_assoc()): ?>
            <div class="quadrado" style="width: <?php echo $row['lado']; ?>px; height: <?php echo $row['lado']; ?>px; background-color: <?php echo $row['cor']; ?>;">
                <span>
                    ID: <?php echo $row['id']; ?><br>
                    Lado: <?php echo $row['lado']; ?><br>
                    <button onclick="editQuadrado(<?php echo $row['id']; ?>, <?php echo $row['lado']; ?>, '<?php echo $row['cor']; ?>')">Editar</button>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                        <button type="submit" name="delete">Excluir</button>
                    </form>
                </span>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>Nenhum quadrado encontrado</p>
    <?php endif; ?>

    <script>
        function editQuadrado(id, lado, cor) {
            document.getElementById('id').value = id;
            document.getElementById('lado').value = lado;
            document.getElementById('cor').value = cor;
        }
    </script>
</body>
</html>

<?php $conn->close(); ?>
