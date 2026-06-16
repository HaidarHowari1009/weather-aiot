# AIoT Weather Monitoring and Prediction System

Aplikasi web berbasis AIoT untuk monitoring dan prediksi cuaca menggunakan data dari BMKG. Aplikasi ini dibangun dengan PHP 8 (Backend), SQLite (Database), Python (Machine Learning - Random Forest), serta Bootstrap 5 & Chart.js (Frontend).

Sistem ini bersifat **Mandiri (Portable)** sehingga **TIDAK MEMBUTUHKAN XAMPP atau MySQL**. Anda dapat langsung menjalankannya menggunakan PHP Built-in Server.

## Persyaratan Sistem

- **Sistem Operasi**: Windows (disarankan)
- **Editor**: Visual Studio Code (VSCode)
- **PHP**: Versi 8.0 atau lebih baru (harus sudah terdaftar di Environment Variables/PATH Windows)
- **Python**: Versi 3.8 atau lebih baru (harus sudah terdaftar di Environment Variables/PATH Windows)

---

## Panduan Menjalankan Aplikasi (di VSCode)

### 1. Buka Proyek di VSCode
1. Buka Visual Studio Code.
2. Klik **File > Open Folder...** dan pilih folder `weather-aiot` ini.

### 2. Setup Lingkungan Python (Satu Kali Saja)
Sistem ini menggunakan Python untuk melatih model dan memprediksi cuaca. Anda perlu menginstal library yang dibutuhkan.
1. Buka Terminal di VSCode (Pilih menu **Terminal > New Terminal**).
2. Pindah ke folder Python dengan mengetikkan perintah:
   ```cmd
   cd python
   ```
3. Install library yang dibutuhkan dengan menjalankan perintah:
   ```cmd
   pip install -r requirements.txt
   ```
4. Jika sudah selesai, kembali ke folder utama dengan mengetik:
   ```cmd
   cd ..
   ```

### 3. Menjalankan Server Aplikasi (PHP)
Agar web bisa dibuka di browser, kita harus menyalakan server bawaan PHP dan mengaktifkan ekstensi SQLite.
1. Pastikan Anda berada di folder utama proyek (`weather-aiot`) di Terminal VSCode.
2. Jalankan perintah berikut untuk menyalakan server:
   ```cmd
      mkdir C:\Users\ASUS\Documents\weather-aiot
   xcopy "C:\Users\ASUS\.gemini\antigravity-ide\scratch\weather-aiot" "C:\Users\ASUS\Documents\weather-aiot" /E /I
   ```
   *(Catatan: Path `C:\php-8.4.8\ext` disesuaikan dengan lokasi instalasi PHP di komputer Anda).*

3. Buka browser (Chrome/Edge/Firefox) dan akses:
   👉 **http://localhost:8000**

---

## Panduan Penggunaan Aplikasi Web

### A. Melatih Model AI (Training)
Model Machine Learning (Random Forest) harus dilatih terlebih dahulu sebelum bisa memprediksi cuaca.
1. Pada aplikasi web, pilih menu **Training Model** di menu sebelah kiri.
2. Klik tombol biru **"Mulai Training Model"**.
3. Sistem PHP akan memanggil Python di latar belakang untuk melatih data dari database. Jika berhasil, akan muncul pesan sukses beserta metrik evaluasinya. (Proses ini menghasilkan file model `.pkl` baru).

### B. Simulasi Data Realtime (BMKG)
1. Aplikasi memiliki fitur mengambil data dari API BMKG secara realtime (saat ini diatur untuk Jakarta Pusat).
2. Data ini akan masuk ke menu **Monitoring Cuaca** secara otomatis jika Anda memicu seeder/API BMKG-nya.

### C. Melakukan Prediksi Cuaca
1. Pilih menu **Prediksi Cuaca**.
2. Masukkan input kondisi cuaca saat ini secara manual (Suhu, Kelembapan, Kecepatan Angin, dan Tutupan Awan).
3. Klik tombol **Prediksi Cuaca**, sistem akan menggunakan model AI yang sudah dilatih untuk memunculkan prediksi kondisi cuaca beserta tingkat keyakinannya (Confidence).

---

**Catatan Tambahan:**
Semua data aplikasi tersimpan aman di dalam file `database/weather.sqlite`. Jika Anda ingin mereset aplikasi, Anda cukup menghapus file `weather.sqlite` tersebut, dan sistem akan otomatis membuat yang baru yang masih kosong saat Anda menjalankan aplikasi kembali.
