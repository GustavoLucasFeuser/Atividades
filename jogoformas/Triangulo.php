<?php

class Triangulo {
    private $id;
    private $lado1;
    private $lado2;
    private $lado3;
    private $cor;
    public $unidade_medida_id;

    public function __construct($id, $lado1, $lado2, $lado3, $cor) {
        $this->id = $id;
        $this->lado1 = $lado1;
        $this->lado2 = $lado2;
        $this->lado3 = $lado3;
        $this->cor = $cor;
    }

    public function criar($conn) {
        $sql = "INSERT INTO forma (tipo, lado1, lado2, lado3, cor, unidade_medida_id) VALUES ('triangulo', ?, ?, ?, ?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ddsss", $this->lado1, $this->lado2, $this->lado3, $this->cor, $this->unidade_medida_id);
            if ($stmt->execute()) {
                $stmt->close();
                return "Triângulo criado com sucesso!";
            } else {
                $stmt->close();
                return "Erro ao criar triângulo: " . $conn->error;
            }
        } else {
            return "Erro na preparação da consulta: " . $conn->error;
        }
    }

    public function editar($conn) {
        $sql = "UPDATE forma SET lado1 = ?, lado2 = ?, lado3 = ?, cor = ?, unidade_medida_id = ? WHERE id = ? AND tipo = 'triangulo'";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ddssi", $this->lado1, $this->lado2, $this->lado3, $this->cor, $this->unidade_medida_id, $this->id);
            if ($stmt->execute()) {
                $stmt->close();
                return "Triângulo atualizado com sucesso!";
            } else {
                $stmt->close();
                return "Erro ao atualizar triângulo: " . $conn->error;
            }
        } else {
            return "Erro na preparação da consulta: " . $conn->error;
        }
    }
}

?>
