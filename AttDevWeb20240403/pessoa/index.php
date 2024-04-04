<?php 
include_once('pessoa.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listagem de pessoas</title>
</head>
<body>
    <!-- Formulário de Cadastro -->
    <h1>CRUD de Contatos</h1>
    <h3><?=$msg?></h3>
    <form action="pessoa.php" method="post">
        <fieldset>
            <legend>Cadastro de Contatos</legend>        
                <label for="id">Id:</label>
                <input type="text" name="id" id="id" value="<?=isset($contato)?$contato->getId():0 ?>" readonly>
                <label for="nome">Nome:</label>
                <input type="text" name="nome" id="nome" value="<?php if(isset($contato)) echo $contato->getNome()?>">
                <label for="telefone">Telefone:</label>
                <input type="text" name="telefone" id="telefone" value="<?php if(isset($contato)) echo $contato->getTelefone()?>">
                <button type='submit' name='acao' value='salvar'>Salvar</button>
                <button type='submit' name='acao' value='excluir'>Excluir</button>
                <button type='reset'>Cancelar</button>
        </fieldset>
    </form>
    <hr>
    <!-- Formulário de pesquisa -->
    <form action="" method="get">
        <fieldset>
            <legend>Pesquisa</legend>
            <label for="busca">Busca:</label>
            <input type="text" name="busca" id="busca" value="">
            <label for="tipo">Tipo:</label>
            <select name="tipo" id="tipo">
                <option value="0">Escolha</option>
                <option value="1">Id</option>
                <option value="2">Nome</option>
                <option value="3">Telefone</option>
            </select>
        <button type='submit'>Buscar</button>

        </fieldset>
    </form>
    <hr>
    <h1>Lista meus contatos</h1>
    <table>
        <tr>
            <th>Id</th>
            <th>Nome</th>
            <th>Telefone</th>
        </tr>
        <?php  
            foreach($lista as $pessoa){ // monta a tabela com base na variável lista, criada no pessoa.php
                echo "<tr><td><a href='index.php?id=".$pessoa->getId()."'>".$pessoa->getId()."</a></td><td>".$pessoa->getNome()."</td><td>".$pessoa->getTelefone()."</td></tr>";
            }     
        ?>
    </table>
</body>
</html>