<?php

class Quadrado {
    private $id;
    private $lado;
    private $cor;

    public function __construct($id, $lado, $cor) {
        $this->id = $id;
        $this->lado = $lado;
        $this->cor = $cor;
    }

    public function getId() {
        return $this->id;
    }

    public function getLado() {
        return $this->lado;
    }

    public function getCor() {
        return $this->cor;
    }

    public function setLado($lado) {
        $this->lado = $lado;
    }

    public function setCor($cor) {
        $this->cor = $cor;
    }


    public function setId($id) {
        $this->id = $id;
    }
    
    public function calcularArea() {
        return $this->lado * $this->lado;
    }

    public function calcularPerimetro() {
        return 4 * $this->lado;
    }
}

function criarQuadrado($conn, $lado, $cor, $unidade_medida_id) {
    $sql = "INSERT INTO forma (tipo, lado, cor, unidade_medida_id) VALUES ('quadrado', '$lado', '$cor', '$unidade_medida_id')";
    if ($conn->query($sql) === TRUE) {
        return "Forma cadastrada com sucesso!";
    } else {
        return "Erro ao cadastrar forma: " . $conn->error;
    }
}

function editarQuadrado($conn, $id, $lado, $cor, $unidade_medida_id) {
    $sql = "UPDATE forma SET lado='$lado', cor='$cor', unidade_medida_id='$unidade_medida_id' WHERE id='$id' AND tipo='quadrado'";
    if ($conn->query($sql) === TRUE) {
        return "Forma atualizada com sucesso!";
    } else {
        return "Erro ao atualizar forma: " . $conn->error;
    }
}

function excluirQuadrado($conn, $id) {
    $sql = "DELETE FROM forma WHERE id='$id' AND tipo='quadrado'";
    if ($conn->query($sql) === TRUE) {
        return "Forma excluída com sucesso!";
    } else {
        return "Erro ao excluir forma: " . $conn->error;
    }
}


?>