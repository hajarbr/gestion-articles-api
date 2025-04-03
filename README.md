# Gestion Articles API

This is a Symfony-based API project for managing articles. It uses MongoDB as the database and is containerized with Docker.

---

## Features

- **Symfony Framework**: Built with Symfony for robust and scalable development.
- **MongoDB**: Uses MongoDB as the database for storing articles.
- **JWT Authentication**: Secured with Lexik JWT Authentication Bundle.
- **CORS Support**: Configured with Nelmio CORS Bundle for cross-origin requests.
- **Dockerized**: Fully containerized with Docker and Docker Compose.

---

## Prerequisites

Make sure you have the following installed on your system:

- [Docker](https://www.docker.com/)
- [Docker Compose](https://docs.docker.com/compose/)

---

## Setup Instructions

1. **Clone the Repository**
    ```bash
    git clone https://github.com/your-username/gestion-articles-api.git
    cd gestion-articles-api
    ```

2. **Configure Environment Variables**
    ```bash
    cp .env.example .env
    ```

    Ensure the following variables are set correctly in the [.env](http://_vscodecontentref_/1) file:

    ```env
    APP_ENV=dev
    APP_SECRET=your_secret_key
    MONGODB_URL=mongodb://mongo:27017/gestion_articles
    MONGODB_DB=gestion_articles
    JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
    JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
    JWT_PASSPHRASE=your_passphrase
    ```

3. **Build and Start the Containers**
    ```bash
    docker-compose build
    docker-compose up -d
    ```

4. **Generate JWT Keys**
    Update the `JWT_PASSPHRASE` in your [.env](http://_vscodecontentref_/2) file and generate the private and public keys:

    ```bash
    mkdir -p config/jwt
    openssl genrsa -out config/jwt/private.pem -aes256 4096
    openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem
    ```

5. **Install Dependencies**
    ```bash
    docker exec -it symfony_app composer install
    ```

6. **Clear Cache**
    ```bash
    docker exec -it symfony_app php bin/console cache:clear
    ```

---

## Unit Testing

This project includes unit tests to ensure the functionality of the application.

### Run Unit Tests

To run the unit tests, execute the following command inside the `symfony_app` container:

```bash
docker exec -it symfony_app php bin/phpunit