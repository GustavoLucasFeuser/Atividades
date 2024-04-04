<?php
/** Controle de Pessoa */

require_once("../classes/Pessoa.class.php");

// Esse trecho avalia se foi enviado um ID na requisição GET - nesse caso o sistema deve apresentar o formulário preenchido com os dados do contato para edição
$id =  isset($_GET['id'])?$_GET['id']:0; // pegar busca
$msg =  isset($_GET['MSG'])?$_GET['MSG']:""; // pegar busca
if ($id > 0){
    $contato = Pessoa::listar(1,$id)[0]; // cria a variável contato que será utilizada para preencher o formulário quando o usuário clicar para alterar um registro
}

// Inserir e alterar dados
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    $id =  isset($_POST['id'])?$_POST['id']:0; 
    $nome =  isset($_POST['nome'])?$_POST['nome']:0; 
    $telefone =  isset($_POST['telefone'])?$_POST['telefone']:0; 
    $acao =  isset($_POST['acao'])?$_POST['acao']:0; 

    try{
        // criar o objeto Pessoa que irá persistir os dados 
        $pessoa = new Pessoa($id,$nome,$telefone);
    }catch(Exception $e){ // caso ocorra algum erro na validação das regras de negócio dispara uma exceção
        header('location: index.php?MSG=Erro: '.$e->getMessage()); // direciona para o incio com a mensagem de erro
    }
    $resultado = "";
    if($acao == 'salvar'){
        if($id > 0)//alterando
            // chamar o método para alterar uma pessoa
            $resultado = $pessoa->alterar();
        else // inserindo                        
            // chamar o método para incluir uma pessoa
            $resultado = $pessoa->incluir();
    }elseif ($acao == 'excluir'){
        // chamar o método para exluir uma pessoa
        $resultado = $pessoa->excluir();
    }
    if ($resultado)
        header('location: index.php?MSG=Dados inseridos/Alterados com sucesso!');
    else
        header('location: index.php?MSG=Erro ao inserir/alterar registro');
}elseif($_SERVER['REQUEST_METHOD'] == 'GET'){ // se a requisição é 
    //  Listagem e Pesquisa
    $busca =  isset($_GET['busca'])?$_GET['busca']:0; // pegar informação da busca
    $tipo =  isset($_GET['tipo'])?$_GET['tipo']:0; // pegar tipo de busca  
    $lista = Pessoa::listar($tipo,$busca); // chama o método listar da classe Pessoa de forma estática (sem criar o objeto Pessoa) para preencher a variável lista que será usada para montar a tabela que lista todos os contatos
}