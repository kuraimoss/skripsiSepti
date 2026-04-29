<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Function ini digunakan untuk menambahkan kolom alamat
     * dan telepon responden pada tabel konsultasi.
     */
    public function up(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            if (! Schema::hasColumn('consultations', 'respondent_address')) {
                $table->text('respondent_address')->nullable()->after('respondent_gender');
            }

            if (! Schema::hasColumn('consultations', 'respondent_phone')) {
                $table->string('respondent_phone', 30)->nullable()->after('respondent_address');
            }
        });
    }

    /**
     * Function ini digunakan untuk menghapus kolom alamat
     * dan telepon responden saat rollback.
     */
    public function down(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            if (Schema::hasColumn('consultations', 'respondent_phone')) {
                $table->dropColumn('respondent_phone');
            }

            if (Schema::hasColumn('consultations', 'respondent_address')) {
                $table->dropColumn('respondent_address');
            }
        });
    }
};
