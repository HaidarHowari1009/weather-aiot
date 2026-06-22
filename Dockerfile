# Menggunakan base image PHP 8.2 dengan server Apache
FROM php:8.2-apache

# Set variabel PORT dari Railway (default ke 80 jika tidak ada)
ENV PORT=80

# Update sistem, install Python 3, pip, dan ekstensi SQLite untuk PHP
# (Kadang instalasi library di Debian memicu update Apache yang menyalakan mpm_event, 
# jadi kita akan matikan secara paksa nanti)
RUN apt-get update && apt-get install -y \
    python3 \
    python3-pip \
    python3-venv \
    libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite \
    && rm -rf /var/lib/apt/lists/*

# Fix error "More than one MPM loaded" secara paksa
RUN rm -f /etc/apache2/mods-enabled/mpm_event.load /etc/apache2/mods-enabled/mpm_worker.load /etc/apache2/mods-enabled/mpm_event.conf /etc/apache2/mods-enabled/mpm_worker.conf
RUN a2enmod mpm_prefork

# Buat virtual environment untuk Python agar library aman
RUN python3 -m venv /opt/venv
ENV PATH="/opt/venv/bin:$PATH"

# Aktifkan mod_rewrite Apache
RUN a2enmod rewrite

# Konfigurasi Apache dibiarkan default di port 80

# Tentukan folder kerja
WORKDIR /var/www/html

# Copy semua file proyek Anda ke dalam server
COPY . /var/www/html/

# Berikan izin akses agar PHP bisa menulis/mengedit database SQLite & file model ML Python
RUN chown -R www-data:www-data /var/www/html/database \
    && chown -R www-data:www-data /var/www/html/python \
    && chmod -R 775 /var/www/html/database \
    && chmod -R 775 /var/www/html/python

# Install library Python dari requirements.txt
RUN pip install --no-cache-dir -r python/requirements.txt

# Expose port 80 untuk web server
EXPOSE 80
