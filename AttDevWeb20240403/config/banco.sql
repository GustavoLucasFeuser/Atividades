-- criar o banco e tabela
create database noticias;
use noticias;
create table noticia (
    id int primary key auto_increment,
    titulo varchar(250),
    conteudo varchar(250)
);
-- inserir dados na tabela
insert into noticia values (null, 'Noticia 1','Conteúdo 1');
insert into noticia values (null, 'Noticia 2','Conteúdo 2');
insert into noticia values (null, 'Noticia 3','Conteúdo 3');
insert into noticia values (null, 'Noticia 4','Conteúdo 4');

-- criar usuário para o banco
create user 'fulano'@'localhost' identified by '123'; -- cria o usuário para acessar ao banco de dados com o login fulano e senha 123
grant all on contatos.* to 'fulano'@'localhost'; -- dá todas as permissões para o usuário fulano no banco contatos