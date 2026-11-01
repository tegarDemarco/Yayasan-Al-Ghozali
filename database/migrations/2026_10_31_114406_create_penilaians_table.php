<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penilaians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('gurus')->cascadeOnDelete();
            $table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();
            $table->string('mata_pelajaran');
            $table->string('kelas');
            $table->string('nama_siswa');
            $table->string('nis')->nullable();
            $table->enum('jenis_penilaian', ['Ulangan Harian', 'UTS', 'UAS', 'Tugas', 'Praktik', 'Portofolio']);
            $table->decimal('nilai', 5, 2);
            $table->string('tahun_ajaran');
            $table->enum('semester', ['Ganjil', 'Genap']);
            $table->date('tanggal_penilaian');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaians');
    }
};
