#!/bin/bash
echo "========================================="
echo "  AIoT Weather - Startup Script"
echo "========================================="

DB_PATH="/var/www/html/database/weather.sqlite"
MODEL_PATH="/var/www/html/database/weather_model.pkl"
ENCODER_PATH="/var/www/html/database/label_encoder.pkl"

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

    # Step 1: Always ensure tables exist
    echo "[INIT] Ensuring database tables exist..."
    cd /var/www/html
    php -r "require_once '/var/www/html/config/database.php';" 2>&1 || true

    # Step 2: Check if weather_data has enough records (need 50+ for training)
    RECORD_COUNT=$(php -r "
        \$pdo = new PDO('sqlite:$DB_PATH');
        \$count = \$pdo->query('SELECT COUNT(*) FROM weather_data')->fetchColumn();
        echo \$count;
    " 2>/dev/null || echo "0")

    echo "[INIT] Current record count: $RECORD_COUNT"

    if [ "$RECORD_COUNT" -lt 50 ] 2>/dev/null; then
        echo "[INIT] Not enough data ($RECORD_COUNT < 50). Running seeder..."
        cd /var/www/html
        php api/seeder.php 2>&1 || true
        echo "[INIT] Seeding complete."
    else
        echo "[INIT] Database has enough data. Skipping seed."
    fi

    # Step 3: Train model if not exists
    if [ ! -f "$MODEL_PATH" ] || [ ! -f "$ENCODER_PATH" ]; then
        echo "[INIT] Training model..."
        cd /var/www/html/python
        /opt/venv/bin/python train_model.py 2>&1 || true
        echo "[INIT] Training complete."
    else
        echo "[INIT] Model already exists. Skipping training."
    fi

    chmod -R 777 /var/www/html/database 2>/dev/null || true
    echo "[INIT] All initialization done!"
    echo "========================================="
} &

# --- Start PHP server immediately ---
echo "Starting PHP server on 0.0.0.0:$PORT"
exec php -S "0.0.0.0:$PORT" -t /var/www/html
