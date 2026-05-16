 PHP 8.3+
 Composer
 Node.js & NPM
 Docker Desktop (for PostgreSQL, Redis, and Mailpit)


Follow these steps to get the project up and running:

1. Clone the repository
```bash
git clone <repository-url>
cd uptime_monitor
```

 2. Install dependencies

```bash
composer install
npm install
```

3. Environment Setup
Copy the example environment file and generate an application key:
```bash
cp .env.example .env
php artisan key:generate
```

Update your `.env` file with the following values to match the Docker services:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=uptime_monitor
DB_USERNAME=postgres
DB_PASSWORD=postgres

REDIS_HOST=127.0.0.1
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="monitor@uptime.local"

QUEUE_CONNECTION=redis
```

4. Start Docker Services
Start the PostgreSQL, Redis, and Mailpit containers:
```bash
docker compose up -d
```

5. Run Migrations
```bash
php artisan migrate
```

6. Start the Web Server
```bash
php artisan serve
```

7. Start the Scheduler
To start processing the uptime checks, you must run the scheduler in a separate terminal:
```bash
php artisan schedule:work
```

Running the Monitor

For the monitoring system to function correctly, two background processes must be running:
1. The Scheduler (`php artisan schedule:work`): Dispatches check jobs every minute.
2. he Queue (`php artisan queue:listen`): Processes the dispatched check jobs.


Check Notifications: Open [Mailpit](http://localhost:8025) to see any outgoing status change notifications.

Test with Local Apps: 
   - Add your existing local development apps to the monitor (e.g., `http://localhost:3000` or `http://localhost:5173`).
   - The monitor will detect if your React/Vite dev servers are running or down.

Service Access

- Web App: [http://localhost:8000](http://localhost:8000)
- Mailpit (Email Testing): [http://localhost:8025](http://localhost:8025)
- PostgreSQL: `localhost:5432`
- Redis: `localhost:6379`

## Testing

Run tests:
```bash
php artisan test
```

Run specific test:
```bash
php artisan test --filter "it updates monitor status to up"
```

Tips:
- Use `->only()` to run a single test.
- Use `--bail` to stop on first failure.
- To verify failures, change `MonitorStatus::UP` to `MonitorStatus::DOWN` in `CheckMonitorJobTest.php:34`.
