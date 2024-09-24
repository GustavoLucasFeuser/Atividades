CREATE DATABASE IF NOT EXISTS geometria;
USE geometria;

CREATE TABLE IF NOT EXISTS unidade_medida (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL,
    simbolo VARCHAR(10) NOT NULL
);

CREATE TABLE IF NOT EXISTS forma (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo ENUM('quadrado', 'circulo') NOT NULL,
    lado FLOAT DEFAULT NULL,
    raio FLOAT DEFAULT NULL,
    cor VARCHAR(7) NOT NULL,
    unidade_medida_id INT,
    FOREIGN KEY (unidade_medida_id) REFERENCES unidade_medida(id)
);