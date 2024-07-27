<?php

class Circulo {
    private $id;
    private $raio;
    private $cor;

    public function __construct($id, $raio, $cor) {
        $this->id = $id;
        $this->raio = $raio;
        $this->cor = $cor;
    }

    public function getId() {
        return $this->id;
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
}
?>
