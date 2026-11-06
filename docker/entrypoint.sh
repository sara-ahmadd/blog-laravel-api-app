#!/bin/sh

set -e

if [ -z "$APP_KEY" ]; then
    echo "Error: APP_KEY is missing"
    exit 1
fi

echo "Waiting for MySQL at ${DB_HOST:-db}:${DB_PORT:-3306}..."

attempts=0
until php -r '
$host = getenv("DB_HOST") ?: "db";
$port = getenv("DB_PORT") ?: "3306";
$user = getenv("DB_USERNAME") ?: "root";
$pass = getenv("DB_PASSWORD") ?: "";
try {
    new PDO(
        "mysql:host={$host};port={$port}",
        $user,
        $pass,
        [PDO::ATTR_TIMEOUT => 3, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    exit(0);
} catch (Throwable $e) {
    fwrite(STDERR, $e->getMessage() . PHP_EOL);
    exit(1);
}
'; do
    attempts=$((attempts + 1))
    if [ "$attempts" -ge 30 ]; then
        echo "Database did not become ready in time."
        exit 1
    fi
    echo "Database is unavailable — waiting..."
    sleep 2
done

echo "Running database migrations..."

php artisan migrate --force

echo "Creating storage symbolic link..."

php artisan storage:link --force

echo "Caching configuration..."

php artisan config:cache

echo "Starting PHP-FPM..."

exec php-fpm
