## _Back-end_
### 🛠 Tecnologies

The following tools were used in building the project:
- [Laravel](https://laravel.com/)
- [Docker](https://www.docker.com/)
- [Docker Compose](https://docs.docker.com/compose/)
- [Maria DB](https://mariadb.org/)
- [phpMyAdmin](https://www.phpmyadmin.net/)

## Requiriments
- MariaDB|MySQL

### 🎲 Running (server)

```bash
# Configure the .env file
DB_CONNECTION=mysql
DB_HOST=book-store-database
DB_PORT=3306
DB_DATABASE=book_store
DB_USERNAME=root
DB_PASSWORD=secret

# install the dependencies
$ tusk composer install

# 1° Opção - running with docker
# running with tusk
$ tusk up

# 2° Opção - running with docker
$ docker compose up -d

# 1° Opção - enter in container
# running with tusk
$ tusk app bash

# 2° Opção - enter in container
$ docker compose exec app bash

# running migrations
$ php artisan migrate

# The API server will launch a port configured in the file .env (padrão 8080) - acesse <http://localhost:8080>
```

### Routes:
| HTTP Method   | Route                     |
| ------------- | ------------------------- |
| POST          | api/auth/forgot-password  |
| POST          | api/auth/login            |
| POST          | api/auth/logout           |
| GET           | api/auth/me               |
| POST          | api/auth/reset-password   |
| POST          | api/auth/signup           |
| GET           | api/books                 |
| POST          | api/books                 |
| GET           | api/books/{bookId}        |
| PUT/PATCH     | api/books/{bookId}        |
| DELETE        | api/books/{bookId}        |
| GET           | api/stores                |
| POST          | api/stores                |
| GET           | api/stores/{storeId}      |
| PUT/PATCH     | api/stores/{storeId}      |
| DELETE        | api/stores/{storeId}      |

