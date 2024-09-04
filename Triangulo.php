<?php


class triangulo {
        private $id;
        private $lado1;
        private $lado2;
        private $lado3;
        private $tipot;
        private $cor;


        public function __construct($id, $lado1, $lado2, $lado3, $tipot, $cor) {
            $this->id = $id;
            $this->lado1 = $lado1;
            $this->lado2 = $lado2;
            $this->lado3 = $lado3;
            $this->tipot = $tipot;
            $this->cor = $cor;
        }
   
        public function getId() {
            return $this->id;
        }
   
        public function getlado1() {
            return $this->lado1;
        }
   
        public function getlado2() {
            return $this->lado2;
        }


        public function getlado3() {
            return $this->lado3;
        }


        public function gettipot() {
            return $this->tipot;
        }


        public function getcor() {
            return $this->cor;
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




};




function CriarTriangulo($conn, $lado1, $lado2, $lado3, $tipo, $cor, $unidade_medida_id) {
    $sql = "INSERT INTO forma (tipo, lado1, lado2, lado3, tipot, cor, unidade_medida_id) VALUES ('triangulo', '$lado1', '$lado2', '$lado3', '$tipot', '$cor', '$unidade_medida_id')";
    if ($conn->query($sql) === TRUE) {
        return "Forma cadastrada com sucesso!";
    } else {
        return "Erro ao cadastrar forma: " . $conn->error;
    }
};


// Determina o tipo do triângulo
if ($lado1 == $lado2 && $lado2 == $lado3) {
    $this->tipo = "Equilatero";
} elseif ($lado1 == $lado2 || $lado1 == $lado3 || $lado2 == $lado3) {
    $this->tipo = "Isosceles";
} else {
    $this->tipo = "Escaleno";
};




function editarTriangulo($conn, $id, $lado1, $lado2, $lado3, $tipot, $cor, $unidade_medida_id) {
    $sql = "UPDATE forma SET lado1='$lado1', lado2='$lado2', lado3='$lado3', tipot='$tipot', cor='$cor', unidade_medida_id='$unidade_medida_id' WHERE id='$id' AND tipo='triangulo'";
    if ($conn->query($sql) === TRUE) {
        return "Forma atualizada com sucesso!";
    } else {
        return "Erro ao atualizar forma: " . $conn->error;
    }
}




function excluirTriangulo($conn, $id) {
    $sql = "DELETE FROM forma WHERE id='$id' AND tipo='triangulo'";
    if ($conn->query($sql) === TRUE) {
        return "Forma excluída com sucesso!";
    } else {
        return "Erro ao excluir forma: " . $conn->error;
    }
}


?>