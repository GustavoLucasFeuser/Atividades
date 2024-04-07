<?php
require_once("../classes/Database.class.php");

class Noticia{
    // Atributos da classe - informações que a classe irá controlar/manter
    private $id; // atributos privados podem ser lidos e escritos somente pelos membros da classe, públicos pode ser manipulados por qualquer outro objeto/programa
    private $titulo; 
    private $conteudo;

    //construtor da classe - permite definir o estado incial do objeto quando instanciado
    public function __construct($id = 0, $titulo = "null", $conteudo = "null"){
        $this->setId($id); // chama os métodos da classe para definir os valores dos atributos, enviando os parâmetros recebidos no construtor, em vez de atribuir direto, assim passa pelas regras de negócio
        $this->setTitulo($titulo);
        $this->setConteudo($conteudo);
    }

    /**
     * Métodos da classe: definem o comportamento do objeto pessoa
     */
    public function setId($novoId){
        if ($novoId < 0)
            throw new Exception("Erro: id inválido!"); //dispara uma exceção
        else
            $this->id = $novoId;
    }
    // função que define (set) o valor de um atributo
    public function setTitulo($novoTitulo){
        if ($novoTitulo == "")
            throw new Exception("Erro: título inválido!");
        else
            $this->titulo = $novoTitulo;
    }
    public function setConteudo($novoConteudo){
        if ($novoConteudo == "")
            throw new Exception("Erro: Conteúdo inválido!");
        else
            $this->conteudo = $novoConteudo;
    }
    // função para ler (get) o valor de um atributo da classe
    public function getId(){
        return $this->id;
    }
    public function getTitulo() { return $this->titulo;}
    public function getConteudo() { return $this->conteudo;}

    /***
     * Inclui uma pessoa no banco  */     
    public function incluir(){
        // abrir conexão com o banco de dados
        $conexao = Database::getInstance(); // chama o método getInstance da classe Database de forma estática para abrir conexão com o banco de dados
        $sql = 'INSERT INTO noticia (titulo, conteudo)
                     VALUES (:titulo, :conteudo)';
        $comando = $conexao->prepare($sql);  // prepara o comando para executar no banco de dados
        $comando->bindValue(':titulo',$this->titulo); // vincula os valores com o comando do banco de dados
        $comando->bindValue(':conteudo',$this->conteudo);
        return $comando->execute(); // executa o comando
    }    
    /** Método para excluir uma pessoa do banco de dados */
    public function excluir(){
        $conexao = Database::getInstance();
        $sql = 'DELETE 
                  FROM noticia
                 WHERE id = :id';
        $comando = $conexao->prepare($sql); 
        $comando->bindValue(':id',$this->id);
        return $comando->execute();
    }  

    /**
     * Essa função altera os dados de uma pessoa no banco de dados
     */
    public function alterar(){
        $conexao = Database::getInstance();
        $sql = 'UPDATE noticia 
                   SET titulo = :titulo, conteudo = :conteudo
                 WHERE id = :id';
        $comando = $conexao->prepare($sql); 
        $comando->bindValue(':id',$this->id);
        $comando->bindValue(':titulo',$this->titulo);
        $comando->bindValue(':conteudo',$this->conteudo);
        return $comando->execute();
    }    

    //** Método estático para listar pessoas - nesse caso não precisa criar um objeto Pessoa para poder chamar esse método */
    public static function listar($tipo = 0, $busca = "" ){
        $conexao = Database::getInstance();
        // montar consulta
        $sql = "SELECT * FROM noticia";        
        if ($tipo > 0 )
            switch($tipo){
                case 1: $sql .= " WHERE id = :busca"; break;
                case 2: $sql .= " WHERE titulo like :busca"; $busca = "%{$busca}%"; break;
                case 3: $sql .= " WHERE conteudo like :busca";  $busca = "%{$busca}%";  break;
            }

        // prepara o comando
        $comando = $conexao->prepare($sql); // preparar comando
        // vincular os parâmetros
        if ($tipo > 0 )
            $comando->bindValue(':busca',$busca);

        // executar consulta
        $comando->execute(); // executar comando
        $pessoas = array(); // cria um vetor para armazenar o resultado da busca
        // listar o resultado da consulta         
        while($registro = $comando->fetch()){
            $pessoa = new Noticia($registro['id'],$registro['titulo'],$registro['conteudo'] ); // cria um objeto pessoa com os dados que vem do banco
            array_push($pessoas,$pessoa); // armazena no vetor pessoas
        }
        return $pessoas;  // retorna o vetor pessoas com uma coleção de objetos do tipo Pessoa
    }    
}

?>