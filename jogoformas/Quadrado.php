<?php

class Quadrado {
    private $id;
    private $lado;
    private $cor;
    private $unidade_medida_id;

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

    public function getUnidadeMedidaId() {
        return $this->unidade_medida_id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setLado($lado) {
        $this->lado = $lado;
    }

    public function setCor($cor) {
        $this->cor = $cor;
    }

    public function setUnidadeMedidaId($unidade_medida_id) {
        $this->unidade_medida_id = $unidade_medida_id;
    }

    public function criar($conn) {
        $sql = "INSERT INTO forma (tipo, lado, cor, unidade_medida_id) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die("Erro na preparação da consulta: " . $conn->error);
        }
        $stmt->bind_param("ssis", $tipo, $lado, $cor, $unidade_medida_id);
        
        // Define os parâmetros
        $tipo = 'quadrado';
        $lado = $this->lado;
        $cor = $this->cor;
        $unidade_medida_id = $this->unidade_medida_id;

        if ($stmt->execute()) {
            return "Quadrado cadastrado com sucesso!";
        } else {
            return "Erro ao cadastrar quadrado: " . $stmt->error;
        }
    }

    public function editar($conn) {
        $sql = "UPDATE forma SET lado=?, cor=?, unidade_medida_id=? WHERE id=? AND tipo='quadrado'";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die("Erro na preparação da consulta: " . $conn->error);
        }
        $stmt->bind_param("ssii", $lado, $cor, $unidade_medida_id, $id);
        
        // Define os parâmetros
        $lado = $this->lado;
        $cor = $this->cor;
        $unidade_medida_id = $this->unidade_medida_id;
        $id = $this->id;

        if ($stmt->execute()) {
            return "Quadrado atualizado com sucesso!";
        } else {
            return "Erro ao atualizar quadrado: " . $stmt->error;
        }
    }
}
?>
