<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Function ini digunakan untuk menambahkan kolom is_admin
     * pada tabel users jika kolom tersebut belum ada.
     */
    public function up(): void
    {
        if (Schema::hasColumn('users', 'is_admin')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false)->after('password');
        });
    }

    /**
     * Function ini digunakan untuk menghapus kolom is_admin
     * dari tabel users saat rollback.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('users', 'is_admin')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });
    }
};
