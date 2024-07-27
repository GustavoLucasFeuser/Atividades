CREATE DATABASE geometria;

USE geometria;

CREATE TABLE forma (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo ENUM('quadrado', 'circulo') NOT NULL,
    lado FLOAT,
    raio FLOAT,
    cor VARCHAR(7) NOT NULL
);
