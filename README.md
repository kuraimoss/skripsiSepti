# Sistem Pakar Deteksi Awal Kesehatan Mental Remaja

Aplikasi web Laravel untuk deteksi awal gangguan kesehatan mental remaja akibat stres lingkungan keluarga dengan metode Dempster-Shafer.

## Stack

- PHP 8.5.5 lokal: `.\.tools\php-8.5.5\php.exe`
- Laravel 13
- PHPUnit 12
- Vite 8 + Tailwind CSS 4
- Database: MySQL/MariaDB via XAMPP, sesuai Bab 2 skripsi

## Setup

Jalankan dari root project `E:\joki\septi`.

```cmd
cd /d E:\joki\septi

composer install
npm install

copy .env.example .env
.tools\php-8.5.5\php.exe artisan key:generate
```

Pastikan Apache/MySQL XAMPP aktif. Buat database lewat phpMyAdmin atau CMD:

```cmd
C:\xampp\mysql\bin\mysql.exe -u root -e "CREATE DATABASE IF NOT EXISTS septi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

Konfigurasi database default:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=septi
DB_USERNAME=root
DB_PASSWORD=
```

Migrasi dan build:

```cmd
.tools\php-8.5.5\php.exe artisan migrate --seed
npm run build
```

Jika Composer di Windows tidak otomatis memakai PHP lokal project, pastikan PHP yang aktif adalah `E:\joki\septi\.tools\php-8.5.5\php.exe` sebelum menjalankan `composer install`.

## Menjalankan Aplikasi

```cmd
.tools\php-8.5.5\php.exe artisan serve --host=127.0.0.1 --port=8000
```

Buka `http://127.0.0.1:8000`.

Untuk mode pengembangan frontend:

```cmd
npm run dev
```

## Database dan Seeder

Reset database dan isi ulang seed:

```cmd
.tools\php-8.5.5\php.exe artisan migrate:fresh --seed
```

Seeder bawaan membuat akun admin:

- Email: `test@example.com`
- Password: `password`

## Menjalankan Test

```cmd
.tools\php-8.5.5\php.exe artisan test
```

Feature test sistem pakar berada di `tests/Feature` dan mencakup halaman konsultasi, submit hasil, dashboard admin, pembatasan akses non-admin, CRUD gejala, CRUD basis pengetahuan, dan pembacaan rule database oleh service Dempster-Shafer.

Route name yang diasumsikan:

- `consultation.create`
- `consultation.store`
- `admin.dashboard`
- `admin.symptoms.index`
- `admin.symptoms.create`
- `admin.symptoms.store`
- `admin.symptoms.edit`
- `admin.symptoms.update`
- `admin.symptoms.destroy`

Seeder menyediakan data penyakit/gangguan, gejala, dan basis pengetahuan awal sesuai data skripsi.
