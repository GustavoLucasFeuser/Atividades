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
    $tipo = $_POST['tipo'];
    $cor = $_POST['cor'];

    if ($tipo == 'quadrado') {
        $lado = $_POST['lado'];
    } else {
        $raio = $_POST['raio'];
    }

    if (isset($_POST['create'])) {
        if ($tipo == 'quadrado') {
            $sql = "INSERT INTO forma (tipo, lado, cor) VALUES ('$tipo', '$lado', '$cor')";
        } else {
            $sql = "INSERT INTO forma (tipo, raio, cor) VALUES ('$tipo', '$raio', '$cor')";
        }
        $conn->query($sql);
    } elseif (isset($_POST['update'])) {
        $id = $_POST['id'];
        if ($tipo == 'quadrado') {
            $sql = "UPDATE forma SET tipo='$tipo', lado='$lado', cor='$cor' WHERE id=$id";
        } else {
            $sql = "UPDATE forma SET tipo='$tipo', raio='$raio', cor='$cor' WHERE id=$id";
        }
        $conn->query($sql);
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $sql = "DELETE FROM forma WHERE id=$id";
        $conn->query($sql);
    }
}

$formas = $conn->query("SELECT * FROM forma ORDER BY tipo, IF(tipo = 'quadrado', lado, raio) DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>CRUD de Formas</title>
    <style>
        .forma {
            display: inline-block;
            margin: 10px;
            text-align: center;
            vertical-align: middle;
            color: black;
            line-height: 1;
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
    <h1>CRUD de Formas</h1>
    <form method="post">
        <input type="hidden" name="id" id="id">
        <label for="tipo">Tipo:</label>
        <select name="tipo" id="tipo" onchange="toggleFields()" required>
            <option value="">Selecione</option>
            <option value="quadrado">Quadrado</option>
            <option value="circulo">Círculo</option>
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
        <button type="submit" name="update">Atualizar</button>
        <button type="submit" name="delete">Excluir</button>
    </form>

    <h2>Listagem de Formas</h2>
    <?php if ($formas->num_rows > 0): ?>
        <?php while($row = $formas->fetch_assoc()): ?>
            <?php if ($row['tipo'] == 'quadrado'): ?>
                <div class="forma" style="width: <?php echo $row['lado']; ?>px; height: <?php echo $row['lado']; ?>px; background-color: <?php echo $row['cor']; ?>;">
                    <span>
                        ID: <?php echo $row['id']; ?><br>
                        Tipo: Quadrado<br>
                        Lado: <?php echo $row['lado']; ?><br>
                        <button onclick="editForma('<?php echo $row['id']; ?>', 'quadrado', '<?php echo $row['lado']; ?>', '', '<?php echo $row['cor']; ?>')">Editar</button>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="delete">Excluir</button>
                        </form>
                    </span>
                </div>
            <?php else: ?>
                <div class="forma" style="width: <?php echo $row['raio'] * 2; ?>px; height: <?php echo $row['raio'] * 2; ?>px; background-color: <?php echo $row['cor']; ?>; border-radius: 50%;">
                    <span>
                        ID: <?php echo $row['id']; ?><br>
                        Tipo: Círculo<br>
                        Raio: <?php echo $row['raio']; ?><br>
                        <button onclick="editForma('<?php echo $row['id']; ?>', 'circulo', '', '<?php echo $row['raio']; ?>', '<?php echo $row['cor']; ?>')">Editar</button>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="delete">Excluir</button>
                        </form>
                    </span>
                </div>
            <?php endif; ?>
        <?php endwhile; ?>
    <?php else: ?>
        <p>Nenhuma forma encontrada</p>
    <?php endif; ?>

    <script>
        function toggleFields() {
            var tipo = document.getElementById('tipo').value;
            document.getElementById('quadrado-fields').style.display = (tipo == 'quadrado') ? 'block' : 'none';
            document.getElementById('circulo-fields').style.display = (tipo == 'circulo') ? 'block' : 'none';
        }

        function editForma(id, tipo, lado, raio, cor) {
            document.getElementById('id').value = id;
            document.getElementById('tipo').value = tipo;
            toggleFields();
            if (tipo == 'quadrado') {
                document.getElementById('lado').value = lado;
                document.getElementById('raio').value = '';
            } else {
                document.getElementById('raio').value = raio;
                document.getElementById('lado').value = '';
            }
            document.getElementById('cor').value = cor;
        }
    </script>
</body>
</html>

<?php $conn->close(); ?>
