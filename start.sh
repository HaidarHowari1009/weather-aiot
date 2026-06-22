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

# --- Step 1: Ensure database directory exists and is writable ---
echo "[1/4] Checking database directory..."
mkdir -p /var/www/html/database
chmod 775 /var/www/html/database

# --- Step 2: Initialize database if not exists ---
if [ ! -f "$DB_PATH" ]; then
    echo "[2/4] Database not found. Creating and seeding..."
    # The PHP config/database.php auto-creates tables on first connection.
    php -r "require_once '/var/www/html/config/database.php';" 2>&1 || echo "  WARNING: Table creation had issues, continuing..."
    echo "  -> Tables created."

    # Seed dummy data for training
    # Use -d flag to set the correct working directory for relative requires
    php -d "include_path=/var/www/html" /var/www/html/api/seeder.php 2>&1 || echo "  WARNING: Seeding had issues, continuing..."
    echo "  -> Dummy data seeded."
else
    echo "[2/4] Database already exists. Skipping seed."
fi

# --- Step 3: Train model if not exists ---
if [ ! -f "$MODEL_PATH" ] || [ ! -f "$ENCODER_PATH" ]; then
    echo "[3/4] Model not found. Training model..."
    cd /var/www/html/python
    timeout 120 /opt/venv/bin/python train_model.py 2>&1 || echo "  WARNING: Model training had issues, continuing..."
    cd /var/www/html
    echo "  -> Model training step completed."
else
    echo "[3/4] Model already exists. Skipping training."
fi

# --- Step 4: Ensure proper permissions ---
echo "[4/4] Setting permissions..."
mkdir -p /var/www/html/assets/images
chmod -R 775 /var/www/html/database 2>/dev/null || true
chmod -R 775 /var/www/html/assets/images 2>/dev/null || true

# --- Start PHP Built-in Server ---
echo ""
echo "========================================="
echo "  Starting PHP server on port $PORT"
echo "========================================="

# Use PHP_CLI_SERVER_WORKERS for multi-process handling (PHP 8.2+)
# This prevents the single-threaded crash issue
export PHP_CLI_SERVER_WORKERS=4
exec php -S 0.0.0.0:${PORT} -t /var/www/html
