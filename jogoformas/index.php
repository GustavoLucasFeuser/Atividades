<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "geometria";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

$message = '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["create"])) {
        $tipo = $_POST["tipo"];
        $cor = $_POST["cor"];
        $unidade_medida_id = $_POST["unidade_medida_id"];

        if ($tipo == 'quadrado') {
            $lado = $_POST["lado"];
            $sql = "INSERT INTO forma (tipo, cor, lado, unidade_medida_id) VALUES ('$tipo', '$cor', '$lado', $unidade_medida_id)";
        } elseif ($tipo == 'circulo') {
            $raio = $_POST["raio"];
            $sql = "INSERT INTO forma (tipo, cor, raio, unidade_medida_id) VALUES ('$tipo', '$cor', '$raio', $unidade_medida_id)";
        }

        if ($conn->query($sql) === TRUE) {
            $message = "Forma cadastrada com sucesso!";
        } else {
            $message = "Erro: " . $sql . "<br>" . $conn->error;
        }
    } elseif (isset($_POST["update"])) {
        $id = $_POST["id"];
        $tipo = $_POST["tipo"];
        $cor = $_POST["cor"];
        $unidade_medida_id = $_POST["unidade_medida_id"];

        if ($tipo == 'quadrado') {
            $lado = $_POST["lado"];
            $sql = "UPDATE forma SET tipo='$tipo', cor='$cor', lado='$lado', unidade_medida_id=$unidade_medida_id WHERE id=$id";
        } elseif ($tipo == 'circulo') {
            $raio = $_POST["raio"];
            $sql = "UPDATE forma SET tipo='$tipo', cor='$cor', raio='$raio', unidade_medida_id=$unidade_medida_id WHERE id=$id";
        }

        if ($conn->query($sql) === TRUE) {
            $message = "Forma atualizada com sucesso!";
        } else {
            $message = "Erro: " . $sql . "<br>" . $conn->error;
        }
    } elseif (isset($_POST["delete"])) {
        $id = $_POST["id"];
        $sql = "DELETE FROM forma WHERE id=$id";

        if ($conn->query($sql) === TRUE) {
            $message = "Forma excluída com sucesso!";
        } else {
            $message = "Erro ao excluir: " . $conn->error;
        }
    }
}

$sql = "SELECT forma.*, unidade_medida.simbolo AS unidade_simbolo FROM forma JOIN unidade_medida ON forma.unidade_medida_id = unidade_medida.id";
if ($search) {
    $sql .= " WHERE tipo LIKE '%$search%' OR cor LIKE '%$search%'";
}
$formas = $conn->query($sql);
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

    <!-- Botão para cadastrar unidades de medida -->
    <p>
        <a href="cadastro_unidade_medida.php">
            <button>Cadastrar Unidade de Medida</button>
        </a>
    </p>

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
            <?php
            $unidades = $conn->query("SELECT id, nome, simbolo FROM unidade_medida");
            while ($unidade = $unidades->fetch_assoc()) {
                echo "<option value='{$unidade['id']}'>{$unidade['nome']} ({$unidade['simbolo']})</option>";
            }
            ?>
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
    </form>

    <h2>Pesquisar Formas</h2>
    <form method="get">
        <input type="text" name="search" placeholder="Buscar por tipo ou cor" value="<?php echo $search; ?>">
        <button type="submit">Buscar</button>
    </form>

    <h2>Listagem de Formas</h2>
    <?php if ($formas && $formas->num_rows > 0): ?>
        <?php while($row = $formas->fetch_assoc()): ?>
            <?php
                $tamanho = $row['tipo'] == 'quadrado' ? $row['lado'] : $row['raio'] * 2;
                $unidade = $row['unidade_simbolo'];
                
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
        <p>Nenhuma forma encontrada.</p>
    <?php endif; ?>

    <script>
        function toggleFields() {
            var tipo = document.getElementById('tipo').value;
            document.getElementById('quadrado-fields').style.display = tipo === 'quadrado' ? 'block' : 'none';
            document.getElementById('circulo-fields').style.display = tipo === 'circulo' ? 'block' : 'none';
        }

        function editForma(id, tipo, lado, raio, cor, unidade_medida_id) {
            document.getElementById('id').value = id;
            document.getElementById('tipo').value = tipo;
            document.getElementById('cor').value = cor;
            document.getElementById('unidade_medida_id').value = unidade_medida_id;
            toggleFields();

            if (tipo === 'quadrado') {
                document.getElementById('lado').value = lado;
            } else {
                document.getElementById('raio').value = raio;
            }
        }
    </script>
</body>
</html>

<?php $conn->close(); ?>
