# Hapster Orders & Products API

A Laravel-based API for managing **Products** and **Orders** with:
- MySQL for persistence
- Redis for caching & queues
- Queue workers for async order processing
- Laravel Horizon for queue monitoring
- Validation, seeders, and tests included

---

## 📦 Prerequisites
- [Docker](https://docs.docker.com/get-docker/) & [Docker Compose](https://docs.docker.com/compose/install/)
- Git

---

## 🚀 Setup & Installation

### 1. Clone the repository
```bash
git clone https://github.com/ali-mansour21/Hapster-Backend-Assessment.git
cd HAPSTER-ASSESSMENT
```

### 2. Create environment file for Docker
Copy the provided `.env.docker.example` into `.env.docker` and update credentials if needed:

```bash
cp .env.docker.example .env.docker
```

Example:

```env
APP_NAME=HapsterAPI
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

---

## 🧪 Running Tests

Run all tests:
```bash
docker compose exec app php artisan test
```
