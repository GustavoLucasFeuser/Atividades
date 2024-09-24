<?php

class Circulo {
    private $id;
    private $raio;
    private $cor;

    public function __construct($id = null, $raio = null, $cor = null) {
        $this->id = $id;
        $this->raio = $raio;
        $this->cor = $cor;
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getRaio() {
        return $this->raio;
    }

    public function getCor() {
        return $this->cor;
    }

    public function setRaio($raio) {
        $this->raio = $raio;
    }

    public function setCor($cor) {
        $this->cor = $cor;
    }

    public function calcularArea() {
        return pi() * $this->raio * $this->raio;
    }

    public function calcularPerimetro() {
        return 2 * pi() * $this->raio;
    }

    public function criar($conn) {
        $sql = "INSERT INTO forma (tipo, raio, cor, unidade_medida_id) VALUES ('circulo', '$this->raio', '$this->cor', '$this->unidade_medida_id')";
        if ($conn->query($sql) === TRUE) {
            return "Círculo cadastrado com sucesso!";
        } else {
            return "Erro ao cadastrar círculo: " . $conn->error;
        }
    }

    public function editar($conn) {
        $sql = "UPDATE forma SET raio='$this->raio', cor='$this->cor', unidade_medida_id='$this->unidade_medida_id' WHERE id='$this->id' AND tipo='circulo'";
        if ($conn->query($sql) === TRUE) {
            return "Círculo atualizado com sucesso!";
        } else {
            return "Erro ao atualizar círculo: " . $conn->error;
        }
    }

    public function excluirCirculo($conn, $id) {
        $sql = "DELETE FROM forma WHERE id='$id' AND tipo='circulo'";
        if ($conn->query($sql) === TRUE) {
            return "Círculo excluído com sucesso!";
        } else {
            return "Erro ao excluir círculo: " . $conn->error;
        }
    }
}
?>