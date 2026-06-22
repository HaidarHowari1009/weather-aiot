#!/bin/bash
echo "========================================="
echo "  AIoT Weather - Startup Script"
echo "========================================="

DB_PATH="/var/www/html/database/weather.sqlite"
MODEL_PATH="/var/www/html/database/weather_model.pkl"
ENCODER_PATH="/var/www/html/database/label_encoder.pkl"

# Railway provides PORT env var
PORT="${PORT:-80}"
echo "PORT=$PORT"

# Ensure directories exist
mkdir -p /var/www/html/database
mkdir -p /var/www/html/assets/images
chmod -R 777 /var/www/html/database 2>/dev/null || true
chmod -R 777 /var/www/html/assets/images 2>/dev/null || true

# --- Background initialization ---
{
    sleep 3

    if [ ! -f "$DB_PATH" ]; then
        echo "[INIT] Creating database..."
        php -r "require_once '/var/www/html/config/database.php';" 2>&1 || true
        cd /var/www/html
        php api/seeder.php 2>&1 || true
        echo "[INIT] Database ready."
    fi

    if [ ! -f "$MODEL_PATH" ] || [ ! -f "$ENCODER_PATH" ]; then
        echo "[INIT] Training model..."
        cd /var/www/html/python
        /opt/venv/bin/python train_model.py 2>&1 || true
        echo "[INIT] Model ready."
    fi

    chmod -R 777 /var/www/html/database 2>/dev/null || true
    echo "[INIT] Done!"
} &

# --- Start PHP server immediately ---
echo "Starting PHP server on 0.0.0.0:$PORT"
exec php -S "0.0.0.0:$PORT" -t /var/www/html
