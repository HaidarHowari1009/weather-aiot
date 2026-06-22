# Menggunakan base image PHP 8.2 versi CLI (Tanpa Apache yang sering error)
FROM php:8.2-cli

# Update sistem, install Python 3, pip, dan ekstensi SQLite untuk PHP
RUN apt-get update && apt-get install -y \
    python3 \
    python3-pip \
    python3-venv \
    libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite \
    && rm -rf /var/lib/apt/lists/*

# Buat virtual environment untuk Python agar library aman
RUN python3 -m venv /opt/venv
ENV PATH="/opt/venv/bin:$PATH"

# Tentukan folder kerja
WORKDIR /var/www/html

# Copy semua file proyek Anda ke dalam server
COPY . /var/www/html/

# Berikan izin akses folder
RUN mkdir -p /var/www/html/assets/images \
    && chown -R www-data:www-data /var/www/html/database \
    && chown -R www-data:www-data /var/www/html/python \
    && chown -R www-data:www-data /var/www/html/assets/images \
    && chmod -R 775 /var/www/html/database \
    && chmod -R 775 /var/www/html/python \
    && chmod -R 775 /var/www/html/assets/images

# Install library Python
RUN pip install --no-cache-dir -r python/requirements.txt

# Jalankan PHP Built-in Server (Sangat stabil di Railway karena otomatis membaca $PORT)
CMD php -S 0.0.0.0:${PORT:-80} -t /var/www/html
