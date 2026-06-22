#!/bin/bash
# DO NOT use set -e — we want the PHP server to start even if init has issues

echo "========================================="
echo "  AIoT Weather - Startup Script"
echo "========================================="

DB_PATH="/var/www/html/database/weather.sqlite"
MODEL_PATH="/var/www/html/database/weather_model.pkl"
ENCODER_PATH="/var/www/html/database/label_encoder.pkl"

# Use Railway's PORT env variable (required!)
PORT="${PORT:-80}"
echo "Using PORT: $PORT"

# --- Ensure directories exist ---
mkdir -p /var/www/html/database
mkdir -p /var/www/html/assets/images
chmod -R 775 /var/www/html/database 2>/dev/null || true
chmod -R 775 /var/www/html/assets/images 2>/dev/null || true

# --- Run initialization in background so PHP server starts IMMEDIATELY ---
(
    sleep 2  # Wait for PHP server to be ready

    echo ""
    echo "[INIT] Starting background initialization..."

    # Step 1: Initialize database if not exists
    if [ ! -f "$DB_PATH" ]; then
        echo "[INIT] Database not found. Creating and seeding..."
        php -r "require_once '/var/www/html/config/database.php';" 2>&1 || echo "[INIT] WARNING: Table creation had issues"
        echo "[INIT] Tables created."

        # Seed dummy data — run from correct directory
        cd /var/www/html
        php /var/www/html/api/seeder.php 2>&1 || echo "[INIT] WARNING: Seeding had issues"
        echo "[INIT] Dummy data seeded."
    else
        echo "[INIT] Database already exists. Skipping seed."
    fi

    # Step 2: Train model if not exists
    if [ ! -f "$MODEL_PATH" ] || [ ! -f "$ENCODER_PATH" ]; then
        echo "[INIT] Model not found. Training model..."
        cd /var/www/html/python
        timeout 180 /opt/venv/bin/python train_model.py 2>&1 || echo "[INIT] WARNING: Model training had issues"
        echo "[INIT] Model training step completed."
    else
        echo "[INIT] Model already exists. Skipping training."
    fi

    # Step 3: Fix permissions after init
    chmod -R 775 /var/www/html/database 2>/dev/null || true
    chmod -R 775 /var/www/html/assets/images 2>/dev/null || true

    echo "[INIT] Background initialization complete!"
    echo "========================================="
) &

# --- Start PHP server IMMEDIATELY (don't wait for init) ---
echo ""
echo "========================================="
echo "  Starting PHP server on port $PORT"
echo "========================================="

# PHP_CLI_SERVER_WORKERS enables multi-process mode (PHP 8.2+)
export PHP_CLI_SERVER_WORKERS=2
exec php -S 0.0.0.0:${PORT} -t /var/www/html
