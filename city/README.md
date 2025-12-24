# Laravel In-Memory City API

![License](https://img.shields.io/badge/license-MIT-blue.svg)
![PHP](https://img.shields.io/badge/php-%5E8.2-777BB4.svg)
![Laravel](https://img.shields.io/badge/laravel-11.x-FF2D20.svg)
![Docker](https://img.shields.io/badge/docker-ready-2496ED.svg)
![CI](https://github.com/username/repo/actions/workflows/tests.yml/badge.svg)

A high-quality, RESTful API for managing City resources, built with **Laravel**.  
Engineered for **Performance**, **Security**, and **Maintainability**.

## 🚀 Features

-   **CRUD Operations**: Full Create, Read, Update, Delete lifecycle.
-   **High Performance**: Cache-First architecture (Memory -> File).
-   **Simulation**: In-Memory persistence.
-   **Dockerized**: Production-ready container setup.
-   **Rate Limiting**: Protected against abuse (60 req/min).
-   **Interactive Documentation**: Integrated Swagger UI.
-   **Admin Tools**: Custom `city:stats` command.
-   **Code Quality**: Static Analysis (Larastan level 5) & Automated CI/CD.

## 📋 Requirements

-   PHP >= 8.2 & Composer OR
-   Docker & Docker Compose

## 🛠️ Installation & Setup

### Option A: Local PHP
1.  `composer install`
2.  `cp .env.example .env`
3.  `php artisan key:generate`
4.  `php artisan serve`

### Option B: Docker (Recommended)
1.  **Build & Start**:
    ```bash
    docker-compose up -d --build
    ```
    The API will be available at `http://localhost:8080`.

## 📖 Documentation

**Interactive Swagger UI**:  
👉 **[http://localhost:8000/api/documentation](http://localhost:8000/api/documentation)** (Local)
👉 **[http://localhost:8080/api/documentation](http://localhost:8080/api/documentation)** (Docker)

## 💻 CLI Commands

**View Data Statistics**:
```bash
php artisan city:stats
```
*(For Docker: `docker-compose exec app php artisan city:stats`)*

## 🔐 Authentication

All API endpoints are protected. Header required:
```http
X-API-KEY: secret-api-key
```

## 📡 API Reference

### Base URL: `/api`

#### 1. List Cities
**GET /cities**
-   `page`, `per_page`
-   `search`: (string) Filter by name/country.
-   `sort_by`: (string) Field.

## 🧪 Quality Assurance

Run the full quality suite:
```bash
# Unit & Feature Tests
php artisan test

# Static Analysis
./vendor/bin/phpstan analyse
```

## 🏗️ Architecture

-   **Repository Pattern**: `FileCityRepository` (Cache + File).
-   **Containerization**: Nginx + PHP-FPM 8.2.
-   **CI/CD**: GitHub Actions workflow (`.github/workflows/tests.yml`).

## 📄 License

[MIT license](https://opensource.org/licenses/MIT).
