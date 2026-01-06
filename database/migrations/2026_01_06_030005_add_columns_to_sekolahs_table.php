<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sekolahs', function (Blueprint $table) {
            // Add GPS coordinates
            $table->decimal('latitude', 10, 8)->nullable()->after('website')->comment('Koordinat GPS');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude')->comment('Koordinat GPS');

            // Add staff counts
            $table->unsignedInteger('jumlah_guru')->default(0)->after('longitude');
            $table->unsignedInteger('jumlah_tu')->default(0)->after('jumlah_guru');
            $table->unsignedInteger('jumlah_siswa')->default(0)->after('jumlah_tu');

            // Add building condition
            $table->enum('status_tanah', ['Milik Sendiri', 'Sewa', 'Pinjam', 'Hibah'])->nullable()->after('jumlah_siswa');
            $table->enum('kondisi_bangunan_umum', ['Baik', 'Rusak Ringan', 'Rusak Sedang', 'Rusak Berat'])->nullable()->after('status_tanah');

            // Add current academic period reference
            $table->unsignedBigInteger('current_academic_period_id')->nullable()->after('kondisi_bangunan_umum');

            // Indexes
            $table->index('jenjang');
            $table->index('akreditasi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sekolahs', function (Blueprint $table) {
            $table->dropColumn([
                'latitude', 'longitude', 'jumlah_guru', 'jumlah_tu',
                'jumlah_siswa', 'status_tanah', 'kondisi_bangunan_umum',
                'current_academic_period_id'
            ]);
        });
    }
};
