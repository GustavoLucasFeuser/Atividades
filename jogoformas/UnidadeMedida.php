<?php

class UnidadeMedida {
    private $id; // ID da unidade de medida
    private $nome; // Nome da unidade (ex: pixel, porcentagem, cm)
    private $simbolo; // Símbolo da unidade (ex: px, %, cm)

    public function __construct($id, $nome, $simbolo) {
        $this->id = $id;
        $this->nome = $nome;
        $this->simbolo = $simbolo;
    }

    // Métodos getters
    public function getId() {
        return $this->id;
    }

    public function getNome() {
        return $this->nome;
    }

    public function getSimbolo() {
        return $this->simbolo;
    }

    // Métodos setters
    public function setNome($nome) {
        $this->nome = $nome;
    }

    public function setSimbolo($simbolo) {
        $this->simbolo = $simbolo;
    }
}
?>