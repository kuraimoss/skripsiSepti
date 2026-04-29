<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Function ini digunakan untuk mendaftarkan service aplikasi
     * jika suatu saat dibutuhkan oleh Laravel.
     */
    public function register(): void {}

    /**
     * Function ini digunakan untuk menjalankan konfigurasi awal
     * saat aplikasi Laravel mulai berjalan.
     */
    public function boot(): void {}
}
