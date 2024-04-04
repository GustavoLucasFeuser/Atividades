-- criar o banco e tabela
create database contatos;
use contatos;
create table pessoa (
    id int primary key auto_increment,
    nome varchar(250),
    telefone varchar(250)
);
-- inserir dados na tabela
insert into pessoa values (null, 'Marcela','124');
insert into pessoa values (null, 'Maria','222');
insert into pessoa values (null, 'Marta','333');
insert into pessoa values (null, 'João','444');

-- criar usuário para o banco
create user 'fulano'@'localhost' identified by '123'; -- cria o usuário para acessar ao banco de dados com o login fulano e senha 123
grant all on contatos.* to 'fulano'@'localhost'; -- dá todas as permissões para o usuário fulano no banco contatos