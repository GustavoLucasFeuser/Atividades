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

// Processa o formulário de cadastro
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $simbolo = $_POST['simbolo'];

    // Insere a nova unidade de medida no banco de dados
    $sql = "INSERT INTO unidade_medida (nome, simbolo) VALUES ('$nome', '$simbolo')";
    
    if ($conn->query($sql) === TRUE) {
        $message = "Unidade de medida cadastrada com sucesso!";
    } else {
        $message = "Erro ao cadastrar unidade de medida: " . $conn->error;
    }
}

// Fecha a conexão
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cadastro de Unidade de Medida</title>
</head>
<body>
    <h1>Cadastro de Unidade de Medida</h1>
    
    <?php if ($message): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="post">
        <label for="nome">Nome da Unidade de Medida:</label>
        <input type="text" name="nome" id="nome" required>
        
        <label for="simbolo">Símbolo:</label>
        <input type="text" name="simbolo" id="simbolo" required>
        
        <button type="submit">Cadastrar</button>
    </form>

    <a href="index.php">Voltar</a>
</body>
</html>
