# Menggunakan base image PHP 8.2 versi CLI
FROM php:8.2-cli

# Update sistem, install Python 3, pip, dan ekstensi SQLite untuk PHP
RUN apt-get update && apt-get install -y \
    python3 \
    python3-pip \
    python3-venv \
    libsqlite3-dev \
    procps \
    && docker-php-ext-install pdo pdo_sqlite \
    && rm -rf /var/lib/apt/lists/*

# Buat virtual environment untuk Python agar library aman
RUN python3 -m venv /opt/venv
ENV PATH="/opt/venv/bin:$PATH"

# Tentukan folder kerja
WORKDIR /var/www/html

# Copy requirements first for better Docker cache
COPY python/requirements.txt /var/www/html/python/requirements.txt

# Install library Python (cached layer)
RUN pip install --no-cache-dir -r python/requirements.txt

# Copy semua file proyek ke dalam container
COPY . /var/www/html/

# Berikan izin akses folder dan startup script
RUN mkdir -p /var/www/html/assets/images \
    && mkdir -p /var/www/html/database \
    && chmod -R 775 /var/www/html/database \
    && chmod -R 775 /var/www/html/python \
    && chmod -R 775 /var/www/html/assets/images \
    && chmod +x /var/www/html/start.sh

# Railway sets PORT env variable automatically, PHP server will use it
# EXPOSE is informational only — Railway uses $PORT
EXPOSE 80

# Gunakan startup script yang menangani initialization + server
CMD ["/bin/bash", "/var/www/html/start.sh"]
