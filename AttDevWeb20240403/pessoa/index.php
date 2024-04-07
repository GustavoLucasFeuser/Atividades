<?php 
include_once('pessoa.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listagem de notícias</title>
</head>
<body>
    <!-- Formulário de Cadastro -->
    <h1>CRUD de Notícias</h1>
    <h3><?=$msg?></h3>
    <form action="pessoa.php" method="post">
        <fieldset>
            <legend>Cadastro de Notícias</legend>        
                <label for="id">Id:</label>
                <input type="text" name="id" id="id" value="<?=isset($contato)?$contato->getId():0 ?>" readonly>
                <label for="nome">Título:</label>
                <input type="text" name="titulo" id="titulo" value="<?php if(isset($contato)) echo $contato->getTitulo()?>">
                <label for="conteudo">Conteúdo:</label>
                <input type="text" name="conteudo" id="conteudo" value="<?php if(isset($contato)) echo $contato->getConteudo()?>">
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
                <option value="2">Título</option>
                <option value="3">Conteúdo</option>
            </select>
        <button type='submit'>Buscar</button>

        </fieldset>
    </form>
    <hr>
    <h1>Lista das notícias</h1>
    <table>
        <tr>
            <th>Id</th>
            <th>Título</th>
            <th>Conteúdo</th>
        </tr>
        <?php  
            foreach($lista as $pessoa){ // monta a tabela com base na variável lista, criada no pessoa.php
                echo "<tr><td><a href='index.php?id=".$pessoa->getId()."'>".$pessoa->getId()."</a></td><td>".$pessoa->getTitulo()."</td><td>".$pessoa->getConteudo()."</td></tr>";
            }     
        ?>
    </table>
</body>
</html>