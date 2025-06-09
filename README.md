# InspireCMS Demo

A demonstration project for InspireCMS, a powerful content management system built with Laravel.

## Prerequisites

-   PHP 8.0 or higher
-   Composer
-   SQLite (or MySQL/PostgreSQL)

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/yourusername/inspirecms-demo.git
cd inspirecms-demo
```

### 2. Install dependencies

```bash
composer install
```

### 3. Configure the application

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Setup the database

SQLite (default):

```bash
cp database/database.sqlite.example database/database.sqlite
```

### 5. Create storage link

```bash
php artisan storage:link
```

### 6. Start the development server

```bash
php artisan serve
```

## Access the application

Visit the URL shown in your terminal after starting the server.

**Demo credentials:**

-   **Username:** demo@solutionforest.net
-   **Password:** 12345678

> Credentials are automatically reset every 30 minutes.
