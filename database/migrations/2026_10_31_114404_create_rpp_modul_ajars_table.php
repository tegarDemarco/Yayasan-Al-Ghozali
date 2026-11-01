<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rpp_modul_ajars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('gurus')->cascadeOnDelete();
            $table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();
            $table->string('judul');
            $table->string('mata_pelajaran');
            $table->string('kelas');
            $table->string('tahun_ajaran');
            $table->enum('semester', ['Ganjil', 'Genap']);
            $table->integer('pertemuan_ke')->nullable();
            $table->string('file_path')->nullable();
            $table->enum('tipe', ['RPP', 'Modul Ajar'])->default('RPP');
            $table->enum('status_validasi', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu');
            $table->text('catatan_validasi')->nullable();
            $table->foreignId('divalidasi_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('divalidasi_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rpp_modul_ajars');
    }
};
