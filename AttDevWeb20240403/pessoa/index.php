<?php 
include_once('pessoa.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listagem de notícias</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <!-- Formulário de Cadastro -->
    <h1 style="text-align:center;">CRUD de Notícias</h1><br>
    <h3><?=$msg?></h3>
    <form action="pessoa.php" method="post">
        <div style="background-color: black; height: 400px; width: 400px; margin-left: 37%; border-radius: 10px;">
        <div style="text-align: center;">
        <fieldset>
            <legend style="color: white; text-align: center;">Cadastro de Notícias</legend>
            <div class="col-md-6">       
                <label for="id" style="color:white;">Id:</label>
                <input class="form-control" type="text" name="id" id="id" value="<?=isset($contato)?$contato->getId():0 ?>" style="margin-left: 45%;"readonly>
            </div>
                <div class="col-md-6">        
                    <label for="nome" style="color:white;">Título:</label>
                    <input class="form-control" type="text" name="titulo" id="titulo" value="<?php if(isset($contato)) echo $contato->getTitulo()?>" style="margin-left: 45%;">
                </div>
                <div class="col-md-6">
                <label for="conteudo" style="color:white;">Conteúdo:</label>
                <input class="form-control" type="text" name="conteudo" id="conteudo" value="<?php if(isset($contato)) echo $contato->getConteudo()?>" style="margin-left: 45%;">
                </div>
                <div style="margin-top: 10%;">
                <button type='submit' name='acao' value='salvar' class="btn btn-dark" >Salvar</button>
                <button type='submit' name='acao' value='excluir' class="btn btn-dark">Excluir</button>

                <button type='reset' class="btn btn-dark">Cancelar</button>
                </div>
        </fieldset>
        </div>
        </div>
    </form><br>
    <hr>
    <!-- Formulário de pesquisa -->
    <form action="" method="get">
        <fieldset>
            <div style="background-color: black; height: 300px; width: 400px; margin-left: 37%; border-radius: 10px;">
            <div style="text-align: center; color:white;">
            <legend style="color: white; text-align: center;">Pesquisa</legend>
            <div style="margin-left:25%;">
            <div class="col-md-8">
            <label for="busca">Busca:</label>
            <input class="form-control" type="text" name="busca" id="busca"><br><br>
            </div>
            </div>
            <label for="tipo">Tipo:</label>
            <select name="tipo" id="tipo">
                <option value="0">Escolha</option>
                <option value="1">Id</option>
                <option value="2">Título</option>
                <option value="3">Conteúdo</option>
            </select>
            <button type='submit' class="btn btn-dark">Buscar</button>
            </div>
        </fieldset>
        </div>
    </form>
    <hr>
    <h1 style="text-align:center;">Lista das notícias</h1><br>
    <div style="margin-left:34%;">
    <div class="col-md-6">
    <table>
        <tr>
            <th>Id</th>
            <th>Título</th>
            <th>Conteúdo</th>
        </tr>
        <?php  
            foreach($lista as $pessoa){ // monta a tabela com base na variável lista, criada no pessoa.php
                echo "<table class='table table-dark table-striped'><tr><td><a href='index.php?id=".$pessoa->getId()."'>".$pessoa->getId()."</a></td><td>".$pessoa->getTitulo()."</td><td>".$pessoa->getConteudo()."</td></tr> </table>";
            }     
        ?>
    </table>
    </div>
    </div>
</body>
</html>