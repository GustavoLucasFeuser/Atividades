<?php
class Triangulo {
    private $id;
    private $base;
    private $altura;
    private $cor;
    public $unidade_medida_id;

    public function __construct($id, $base, $altura, $cor) {
        $this->id = $id;
        $this->base = $base;
        $this->altura = $altura;
        $this->cor = $cor;
    }

    public function criar($conn) {
        $sql = "INSERT INTO forma (tipo, base, altura, cor, unidade_medida_id) VALUES ('triangulo', '$this->base', '$this->altura', '$this->cor', '$this->unidade_medida_id')";
        if ($conn->query($sql) === TRUE) {
            return "Triângulo criado com sucesso!";
        } else {
            return "Erro ao criar triângulo: " . $conn->error;
        }
    }

    public function editar($conn) {
        $sql = "UPDATE forma SET base='$this->base', altura='$this->altura', cor='$this->cor', unidade_medida_id='$this->unidade_medida_id' WHERE id='$this->id'";
        if ($conn->query($sql) === TRUE) {
            return "Triângulo atualizado com sucesso!";
        } else {
            return "Erro ao atualizar triângulo: " . $conn->error;
        }
    }
}

?>