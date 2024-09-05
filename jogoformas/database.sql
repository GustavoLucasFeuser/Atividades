CREATE DATABASE IF NOT EXISTS geometria;
USE geometria;

CREATE TABLE IF NOT EXISTS unidade_medida (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL,
    simbolo VARCHAR(10) NOT NULL
);

CREATE TABLE forma (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo VARCHAR(50) NOT NULL,
    lado FLOAT,
    raio FLOAT,
    lado1 FLOAT,
    lado2 FLOAT,
    lado3 FLOAT,
    cor VARCHAR(7),
    unidade_medida_id INT,
    FOREIGN KEY (unidade_medida_id) REFERENCES unidade_medida(id)
);
