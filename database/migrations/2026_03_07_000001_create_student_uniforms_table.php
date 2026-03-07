<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_uniforms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();
            $table->foreignId('academic_period_id')->constrained('academic_periods')->cascadeOnDelete();
            $table->string('nama_siswa');
            $table->string('nisn', 20)->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->comment('L=Laki-laki, P=Perempuan');
            $table->enum('kelas', ['I', 'II', 'III', 'IV', 'V', 'VI']);
            $table->enum('ukuran_baju', ['S', 'M', 'L', 'XL', 'XXL', 'XXXL']);
            $table->enum('ukuran_celana_rok', ['S', 'M', 'L', 'XL', 'XXL', 'XXXL']);
            $table->unsignedTinyInteger('ukuran_sepatu');
            $table->timestamps();

            $table->index(['sekolah_id', 'academic_period_id', 'kelas']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_uniforms');
    }
};
