#!/bin/bash
set -e

echo "========================================="
echo "  AIoT Weather - Startup Script"
echo "========================================="

DB_PATH="/var/www/html/database/weather.sqlite"
MODEL_PATH="/var/www/html/database/weather_model.pkl"
ENCODER_PATH="/var/www/html/database/label_encoder.pkl"

# --- Step 1: Ensure database directory exists and is writable ---
echo "[1/4] Checking database directory..."
mkdir -p /var/www/html/database
chmod 775 /var/www/html/database

# --- Step 2: Initialize database if not exists ---
if [ ! -f "$DB_PATH" ]; then
    echo "[2/4] Database not found. Creating and seeding..."
    # The PHP config/database.php auto-creates tables on first connection.
    # We just need to seed data by calling the seeder via PHP.
    php -r "require_once '/var/www/html/config/database.php';"
    echo "  -> Tables created."
    
    # Seed dummy data for training
    php /var/www/html/api/seeder.php
    echo "  -> Dummy data seeded."
else
    echo "[2/4] Database already exists. Skipping seed."
fi

# --- Step 3: Train model if not exists ---
if [ ! -f "$MODEL_PATH" ] || [ ! -f "$ENCODER_PATH" ]; then
    echo "[3/4] Model not found. Training model..."
    cd /var/www/html/python
    /opt/venv/bin/python train_model.py
    cd /var/www/html
    echo "  -> Model trained successfully."
else
    echo "[3/4] Model already exists. Skipping training."
fi

# --- Step 4: Ensure proper permissions ---
echo "[4/4] Setting permissions..."
mkdir -p /var/www/html/assets/images
chmod -R 775 /var/www/html/database
chmod -R 775 /var/www/html/assets/images

# --- Start PHP Built-in Server ---
echo ""
echo "========================================="
echo "  Starting PHP server on port ${PORT:-80}"
echo "========================================="
exec php -S 0.0.0.0:${PORT:-80} -t /var/www/html
