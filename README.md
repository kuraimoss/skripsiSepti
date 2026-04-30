# Sistem Pakar Deteksi Awal Kesehatan Mental Remaja

Aplikasi web Laravel untuk deteksi awal gangguan kesehatan mental remaja akibat stres lingkungan keluarga dengan metode Dempster-Shafer.

## Stack Project

- PHP 8.3 atau lebih baru
- Laravel 13
- MySQL via XAMPP
- Composer
- Node.js dan npm
- Vite 8
- Tailwind CSS 4
- PHPUnit 12

Database yang dipakai project ini hanya MySQL.

## Atribusi Asset

- Ilustrasi hero menggunakan asset `Online doctor` dari [Storyset by Freepik](https://storyset.com/medical).

## Clone Project

Jalankan perintah berikut dari CMD.

```cmd
cd /d E:\joki
git clone https://github.com/kuraimoss/skripsiSepti.git septi
cd septi
```

Jika folder tujuan berbeda, sesuaikan path `cd` dengan lokasi project di komputer.

## Install Dependency

Install dependency Laravel dan frontend.

```cmd
composer install
npm install
```

Jika memakai PHP lokal yang ada di workspace ini, perintah artisan dapat dijalankan dengan:

```cmd
.\.tools\php-8.5.5\php.exe artisan --version
```

Jika PHP sudah terpasang di sistem atau dari XAMPP, cukup gunakan:

```cmd
php artisan --version
```

Pada panduan berikutnya, perintah ditulis memakai `php artisan`. Jika di komputer memakai PHP lokal project, ganti `php` dengan `.\.tools\php-8.5.5\php.exe`.

## Konfigurasi Environment

Buat file `.env` dari `.env.example`.

```cmd
copy .env.example .env
```

Generate application key Laravel.

```cmd
php artisan key:generate
```

Pastikan konfigurasi database di `.env` memakai MySQL seperti berikut.

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=septi
DB_USERNAME=root
DB_PASSWORD=
```

Keterangan:

- `DB_DATABASE=septi` adalah nama database yang akan dibuat.
- `DB_USERNAME=root` adalah user default MySQL XAMPP.
- `DB_PASSWORD=` dikosongkan jika MySQL XAMPP tidak memakai password.

## Membuat Database MySQL

Nyalakan MySQL dari XAMPP Control Panel terlebih dahulu.

Lalu buat database melalui CMD:

```cmd
C:\xampp\mysql\bin\mysql.exe -u root -e "CREATE DATABASE IF NOT EXISTS septi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

Jika MySQL memakai password, gunakan perintah ini lalu masukkan password saat diminta:

```cmd
C:\xampp\mysql\bin\mysql.exe -u root -p -e "CREATE DATABASE IF NOT EXISTS septi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

Database juga bisa dibuat dari phpMyAdmin dengan nama `septi`.

## Migrasi dan Seeder Database

Jalankan migration dan seeder agar semua tabel serta data awal sistem pakar terisi.

```cmd
php artisan migrate --seed
```

Jika ingin mengulang database dari awal:

```cmd
php artisan migrate:fresh --seed
```

Seeder akan mengisi:

- Data gangguan sesuai skripsi
- Data gejala sesuai skripsi
- Basis pengetahuan Dempster-Shafer
- Akun admin bawaan

Akun admin bawaan:

```txt
Email    : test@example.com
Password : password
```

## Build Asset Frontend

Untuk menjalankan tampilan dengan asset production:

```cmd
npm run build
```

Untuk mode development frontend:

```cmd
npm run dev
```

Jika memakai `npm run dev`, biarkan terminal tersebut tetap menyala selama aplikasi dipakai.

## Menjalankan Program

Jalankan server Laravel:

```cmd
php artisan serve --host=127.0.0.1 --port=8000
```

Buka aplikasi di browser:

```txt
http://127.0.0.1:8000
```

Halaman admin:

```txt
http://127.0.0.1:8000/login
```

Login admin memakai akun bawaan dari seeder.

## Urutan Cepat dari Awal

Jika ingin menjalankan project dari nol, gunakan urutan ini.

```cmd
cd /d E:\joki
git clone https://github.com/kuraimoss/skripsiSepti.git septi
cd septi

composer install
npm install

copy .env.example .env
php artisan key:generate

C:\xampp\mysql\bin\mysql.exe -u root -e "CREATE DATABASE IF NOT EXISTS septi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

php artisan migrate --seed
npm run build
php artisan serve --host=127.0.0.1 --port=8000
```

Setelah itu buka:

```txt
http://127.0.0.1:8000
```

## Menjalankan Test

Jalankan test aplikasi:

```cmd
php artisan test
```

Jalankan formatter Laravel Pint:

```cmd
vendor\bin\pint
```

Jalankan pengecekan format tanpa mengubah file:

```cmd
vendor\bin\pint --test
```

## Troubleshooting

Jika muncul error `Access denied for user`, cek kembali `DB_USERNAME` dan `DB_PASSWORD` di `.env`.

Jika muncul error database tidak ditemukan, pastikan database `septi` sudah dibuat di MySQL.

Jika perubahan `.env` belum terbaca, jalankan:

```cmd
php artisan config:clear
php artisan cache:clear
```

Jika tampilan CSS belum berubah atau tidak rapi, jalankan:

```cmd
npm run build
```

Jika package belum lengkap, ulangi:

```cmd
composer install
npm install
```
