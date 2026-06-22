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

# Fix error "More than one MPM loaded":
# apt-get install can re-enable mpm_event by restoring its symlinks, so we
# forcibly remove the symlinks first, then use a2dismod/a2enmod, and finally
# assert that exactly one MPM .load file remains enabled.
RUN rm -f /etc/apache2/mods-enabled/mpm_event.conf \
          /etc/apache2/mods-enabled/mpm_event.load \
          /etc/apache2/mods-enabled/mpm_worker.conf \
          /etc/apache2/mods-enabled/mpm_worker.load \
    && a2dismod mpm_event mpm_worker || true \
    && a2enmod mpm_prefork \
    && test "$(ls /etc/apache2/mods-enabled/mpm_*.load 2>/dev/null | wc -l)" -eq 1

# Explicitly disable other MPMs in apache2.conf to prevent runtime loading
RUN echo "" >> /etc/apache2/apache2.conf && \
    echo "# Disable event and worker MPMs, use only prefork" >> /etc/apache2/apache2.conf && \
    echo "LoadModule mpm_prefork_module /usr/lib/apache2/modules/mod_mpm_prefork.so" >> /etc/apache2/apache2.conf

# Buat virtual environment untuk Python agar library aman
RUN python3 -m venv /opt/venv
ENV PATH="/opt/venv/bin:$PATH"

# Aktifkan mod_rewrite Apache
RUN a2enmod rewrite

# Konfigurasi Apache agar menggunakan $PORT dari Railway alih-alih port 80 statis
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

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

# Expose port (meskipun Railway mendeteksinya secara dinamis)
EXPOSE ${PORT}
