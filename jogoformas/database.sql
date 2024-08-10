-- Criação do banco de dados 'geometria' (se ainda não existir)
CREATE DATABASE IF NOT EXISTS geometria;
USE geometria;

-- Tabela de Unidades de Medida
CREATE TABLE unidade_medida (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL,
    simbolo VARCHAR(10) NOT NULL
);

-- Atualização da tabela 'forma' para adicionar a chave estrangeira para unidade de medida
ALTER TABLE forma
ADD COLUMN unidade_medida_id INT,
ADD CONSTRAINT fk_unidade_medida FOREIGN KEY (unidade_medida_id) REFERENCES unidade_medida(id);

