# Hapster Orders & Products API

A Laravel-based API for managing **Products** and **Orders** with:
- MySQL for persistence
- Redis for caching & queues
- Queue workers for async order processing
- Optional Laravel Horizon for queue monitoring
- Validation, seeders, and tests included

---

## 📦 Prerequisites
- [Docker](https://docs.docker.com/get-docker/) & [Docker Compose](https://docs.docker.com/compose/install/)
- Git

> **💡 Alternative Setup**: This project can also work directly on Windows without Docker, but you'll need to remove the Laravel Horizon package first (see installation notes below).

---

## 🚀 Setup & Installation

### 1. Clone the repository
```bash
git clone https://github.com/ali-mansour21/Hapster-Backend-Assessment.git
cd Hapster-Backend-Assessment
```

### 2. Create environment files

#### For Docker database credentials:
Copy the provided `.env.docker.example` into `.env.docker` and update database credentials:

```bash
cp .env.docker.example .env.docker
```

#### For Laravel application:
Copy the Laravel environment example file:

```bash
cp .env.example .env
```

Update the `.env` file with the following configuration:

```env
APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:GENERATE_ME
APP_DEBUG=true
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=hapster
DB_USERNAME=hapster
DB_PASSWORD=hapster

CACHE_STORE=redis
QUEUE_CONNECTION=redis
REDIS_HOST=redis
REDIS_PORT=6379
```

Make sure the database credentials match those in your `.env.docker` file.

### 3. Build & start containers
```bash
docker compose --env-file .env.docker up -d --build
```

Services started:
- **app**: Laravel PHP-FPM container
- **db**: MySQL database
- **redis**: Redis for cache & queues
- **worker**: Laravel queue worker (`php artisan queue:work`)
- **horizon**: Laravel Horizon for monitoring queues

### 4. Install dependencies inside container

> **⚠️ Windows Users Note**: If you're running this project directly on Windows (not using Docker), remove the Laravel Horizon package before running `composer install` since the required extension is not available on Windows (Linux only):
> ```bash
> composer remove laravel/horizon
> ```
> Then proceed with the installation.

```bash
docker compose exec app composer install
```

### 5. Generate app key
```bash
docker compose exec app php artisan key:generate
```

### 6. Run migrations & seeders
```bash
docker compose exec app php artisan migrate --seed
```

This will create schema and seed sample products.

---

## 🛠 Running the Application

### API server
The Laravel app will be available at:
```
http://localhost
```

### Queue worker
A dedicated queue worker service runs automatically via `worker` container.

If you want to run manually:
```bash
docker compose exec app php artisan queue:work
```

### Laravel Horizon (optional)
A dedicated horizon worker service runs automatically via `horizon` container.

If you want to run manually:
```bash
docker compose exec app php artisan queue:work
```
Access it in your browser at:
```
http://localhost/horizon
```

> **Note**: In production, protect `/horizon` with authentication.

---

## 🧪 Running Tests

Run all tests:
```bash
docker compose exec app php artisan test
```

---

## 📡 API Testing

### Create a Product

```http
POST /api/products
Content-Type: application/json

{
  "name": "Mouse",
  "sku": "SKU-MOU-001",
  "price": 24.90,
  "stock": 100
}
```

### Update a Product

```http
PUT /api/products/{id}
Content-Type: application/json

{
  "price": 29.90,
  "stock": 80
}
```

### Create an Order

```http
POST /api/orders
Content-Type: application/json

{
  "items": [
    { "product_id": 1, "qty": 2 },
    { "product_id": 2, "qty": 1 }
  ]
}
```

### 📊 Order Statistics Endpoint

Get order statistics:

```http
GET /api/orders/statistics
```

Returns:

```json
{
  "count": 10,
  "total_revenue": "1549.50"
}
```
