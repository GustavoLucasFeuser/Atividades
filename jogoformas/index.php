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

        if (isset($_POST['update'])) {
            // Atualiza forma existente
            $id = $_POST['id'];
            if ($tipo == 'quadrado') {
                $lado = isset($_POST['lado']) ? $_POST['lado'] : '';
                $sql = "UPDATE forma SET tipo='$tipo', lado='$lado', cor='$cor', unidade_medida_id='$unidade_medida_id' WHERE id='$id'";
            } else {
                $raio = isset($_POST['raio']) ? $_POST['raio'] : '';
                $sql = "UPDATE forma SET tipo='$tipo', raio='$raio', cor='$cor', unidade_medida_id='$unidade_medida_id' WHERE id='$id'";
            }
        } else {
            // Insere nova forma
            if ($tipo == 'quadrado') {
                $lado = isset($_POST['lado']) ? $_POST['lado'] : '';
                $sql = "INSERT INTO forma (tipo, lado, cor, unidade_medida_id) VALUES ('$tipo', '$lado', '$cor', '$unidade_medida_id')";
            } else {
                $raio = isset($_POST['raio']) ? $_POST['raio'] : '';
                $sql = "INSERT INTO forma (tipo, raio, cor, unidade_medida_id) VALUES ('$tipo', '$raio', '$cor', '$unidade_medida_id')";
            }
        }

        if ($conn->query($sql) === TRUE) {
            $message = isset($_POST['update']) ? "Forma atualizada com sucesso!" : "Forma cadastrada com sucesso!";
        } else {
            $message = "Erro ao cadastrar/atualizar forma: " . $conn->error;
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
        </select>

        <label for="search_cor">Cor:</label>
        <input type="text" name="search_cor" id="search_cor" placeholder="#RRGGBB">

        <label for="search_lado">Lado:</label>
        <input type="text" name="search_lado" id="search_lado">

        <label for="search_raio">Raio:</label>
        <input type="text" name="search_raio" id="search_raio">

        <button type="submit" name="search">Pesquisar</button>
    </form>

    <?php if ($formas && $formas->num_rows > 0): ?>
        <?php while($row = $formas->fetch_assoc()): ?>
            <?php
                // Define o tamanho da forma de acordo com o tipo
                $unidade = $row['unidade_simbolo'];
                $style = '';
                if ($row['tipo'] === 'quadrado') {
                    $style = "width: {$row['lado']}{$unidade}; height: {$row['lado']}{$unidade};";
                    $cor_hex = $row['cor'];
                } else {
                    $style = "width: {$row['raio']}{$unidade}; height: {$row['raio']}{$unidade}; border-radius: 50%;";
                    $cor_hex = $row['cor'];
                }
            ?>
            <?php if ($row['tipo'] === 'quadrado'): ?>
                <div class="forma" style="background-color: <?php echo $cor_hex; ?>; <?php echo $style; ?>">
                    <span>
                        ID: <?php echo $row['id']; ?><br>
                        Tipo: Quadrado<br>
                        Lado: <?php echo $row['lado']; ?> <?php echo $row['unidade_simbolo']; ?><br>
                        Cor: <?php echo $cor_hex; ?><br>
                        <button onclick="editForma('<?php echo $row['id']; ?>', 'quadrado', '<?php echo $row['lado']; ?>', '', '<?php echo $cor_hex; ?>', '<?php echo $row['unidade_medida_id']; ?>')">Editar</button>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="delete">Excluir</button>
                        </form>
                    </span>
                </div>
            <?php else: ?>
                <div class="forma" style="background-color: <?php echo $cor_hex; ?>; <?php echo $style; ?>">
                    <span>
                        ID: <?php echo $row['id']; ?><br>
                        Tipo: Círculo<br>
                        Raio: <?php echo $row['raio']; ?> <?php echo $row['unidade_simbolo']; ?><br>
                        Cor: <?php echo $cor_hex; ?><br>
                        <button onclick="editForma('<?php echo $row['id']; ?>', 'circulo', '', '<?php echo $row['raio']; ?>', '<?php echo $cor_hex; ?>', '<?php echo $row['unidade_medida_id']; ?>')">Editar</button>
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
            var tipo = document.getElementById("tipo").value;
            document.getElementById("quadrado-fields").style.display = tipo == "quadrado" ? "block" : "none";
            document.getElementById("circulo-fields").style.display = tipo == "circulo" ? "block" : "none";
        }

        function editForma(id, tipo, lado, raio, cor, unidade_medida_id) {
            document.getElementById("id").value = id;
            document.getElementById("tipo").value = tipo;
            document.getElementById("lado").value = lado;
            document.getElementById("raio").value = raio;
            document.getElementById("cor").value = cor;
            document.getElementById("unidade_medida_id").value = unidade_medida_id;

            // Mostra campos correspondentes
            toggleFields();
        }
    </script>
</body>
</html>
