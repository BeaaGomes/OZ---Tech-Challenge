# Back-end Challenge 🏅 Space Flight News

## Visão geral  

O projeto consiste em uma **REST API** para criar, listar, atualizar e deletar artigos jornalísticos sobre voos espaciais. Além de consumir diariamente a API externa Space Flight News para a constante atualização dos artigos.

## Tecnologias utilizadas

- PHP 8.0.5 
- Laravel 9
- PHPUnit 9.5
- MySQL 
- Heroku
- Docker
 

## Setup do projeto  

Clone esse repositório.

    git clone https://github.com/BeaaGomes/OZ-Tech-Challenge.git
    
Acesse o diretório da api.    

    cd oz-tech-challenge  

Copie o env de exemplo.  

    cp .env.example .env  

Crie um banco de dados vazio e coloque as credenciais no `.env`.

Preencha as variáveis relacionadas ao envio de emails no `.env`.

Preencha no `.env` a variável `ALERT_MAIL_TO_ADDRESS` com o endereço de email que receberá os alertas em caso de erro na sincronização diária dos artigos.

Suba o container do docker.  

    docker compose up
    
Em um outro terminal, acesse o container do docker.

    docker exec -it oz_tech_challenge bash
    
Dentro desse container, rode as migrations.

    php artisan migrate

O servidor agora está disponível em http://localhost:8000  

## Como usar

### Rotas

- `[GET]/: ` Retorna a mensagem "Back-end Challenge 2021 🏅 - Space Flight News".
- `[GET]/articles/:` Lista os artigos, paginando de 10 em 10. É necessário passar uma `page` no body da requisição.
- `[GET]/articles/{id}:` Retorna um artigo baseado no `id`.
- `[POST]/articles/:` Adiciona um novo artigo. Caso o usuário envie `launches` ou `events` que ainda não existem em nossa base, também os adiciona.
- `[PUT]/articles/{id}: `Atualiza um artigo baseado no `id`. Caso o usuário envie `launches` ou `events` que ainda não existem em nossa base, os adiciona.
- `[DELETE]/articles/{id}:` Remove um artigo baseado no `id`.

### Testes

Para rodar os testes automatizados utilize o comando:

    php artisan test

Os testes cobrem o funcionamento de todos os endpoints existentes. Lembre-se de sincronizar os artigos pelo menos uma vez antes de executar os testes.

### Sincronização dos dados

Além de ocorrer diariamente às 09:00 é possível atualizar os artigos "manualmente" através do comando:

    php artisan command:FetchArticlesFromSpaceflightNews

Sua utilização é útil para trazer todos os artigos da API externa para o banco recém criado.

>  This is a challenge by [Coodesh](https://coodesh.com/)
