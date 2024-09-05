<?php

class Triangulo {
    private $id;
    private $lado1;
    private $lado2;
    private $lado3;
    private $tipot;
    private $cor;
    private $unidade_medida_id; // Adicionado para armazenar unidade_medida_id

    public function __construct($id, $lado1, $lado2, $lado3, $cor) {
        $this->id = $id;
        $this->lado1 = $lado1;
        $this->lado2 = $lado2;
        $this->lado3 = $lado3;
        $this->cor = $cor;

        // Determina o tipo do triângulo no construtor
        if ($lado1 == $lado2 && $lado2 == $lado3) {
            $this->tipot = "Equilatero";
        } elseif ($lado1 == $lado2 || $lado1 == $lado3 || $lado2 == $lado3) {
            $this->tipot = "Isosceles";
        } else {
            $this->tipot = "Escaleno";
        }
    }

    public function getId() {
        return $this->id;
    }

    public function getLado1() {
        return $this->lado1;
    }

    public function getLado2() {
        return $this->lado2;
    }

    public function getLado3() {
        return $this->lado3;
    }

    public function getTipot() {
        return $this->tipot;
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

    public function setLado1($lado1) {
        $this->lado1 = $lado1;
    }

    public function setLado2($lado2) {
        $this->lado2 = $lado2;
    }

    public function setLado3($lado3) {
        $this->lado3 = $lado3;
    }

    public function setTipot($tipot) {
        $this->tipot = $tipot;
    }

    public function setCor($cor) {
        $this->cor = $cor;
    }

    public function setUnidadeMedidaId($unidade_medida_id) {
        $this->unidade_medida_id = $unidade_medida_id;
    }

    // Função para criar triângulo no banco de dados
    public function criar($conn) {
        $sql = "INSERT INTO forma (tipo, lado1, lado2, lado3, tipot, cor, unidade_medida_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die("Erro na preparação da consulta: " . $conn->error);
        }
        $stmt->bind_param("ssssssi", $tipo, $lado1, $lado2, $lado3, $tipot, $cor, $unidade_medida_id);
        
        // Define os parâmetros
        $tipo = 'triangulo';
        $lado1 = $this->lado1;
        $lado2 = $this->lado2;
        $lado3 = $this->lado3;
        $tipot = $this->tipot;
        $cor = $this->cor;
        $unidade_medida_id = $this->unidade_medida_id;

        if ($stmt->execute()) {
            return "Triângulo cadastrado com sucesso!";
        } else {
            return "Erro ao cadastrar triângulo: " . $stmt->error;
        }
    }

    // Função para editar triângulo no banco de dados
    public function editar($conn) {
        $sql = "UPDATE forma SET lado1=?, lado2=?, lado3=?, tipot=?, cor=?, unidade_medida_id=? WHERE id=? AND tipo='triangulo'";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die("Erro na preparação da consulta: " . $conn->error);
        }
        $stmt->bind_param("ssssssi", $lado1, $lado2, $lado3, $tipot, $cor, $unidade_medida_id, $id);
        
        // Define os parâmetros
        $lado1 = $this->lado1;
        $lado2 = $this->lado2;
        $lado3 = $this->lado3;
        $tipot = $this->tipot;
        $cor = $this->cor;
        $unidade_medida_id = $this->unidade_medida_id;
        $id = $this->id;

        if ($stmt->execute()) {
            return "Triângulo atualizado com sucesso!";
        } else {
            return "Erro ao atualizar triângulo: " . $stmt->error;
        }
    }

    // Função para excluir triângulo no banco de dados
    public function excluir($conn) {
        $sql = "DELETE FROM forma WHERE id=? AND tipo='triangulo'";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die("Erro na preparação da consulta: " . $conn->error);
        }
        $stmt->bind_param("i", $id);
        
        // Define o parâmetro
        $id = $this->id;

        if ($stmt->execute()) {
            return "Triângulo excluído com sucesso!";
        } else {
            return "Erro ao excluir triângulo: " . $stmt->error;
        }
    }
}
?>
