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

    public function calcularArea() {
        return $this->lado * $this->lado;
    }

    public function calcularPerimetro() {
        return 4 * $this->lado;
    }
}
?>
