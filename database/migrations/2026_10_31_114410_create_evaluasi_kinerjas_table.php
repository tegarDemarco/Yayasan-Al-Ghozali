<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluasi_kinerjas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('gurus')->cascadeOnDelete();
            $table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();
            $table->string('tahun_ajaran');
            $table->enum('semester', ['Ganjil', 'Genap']);
            $table->decimal('nilai_kinerja', 5, 2)->default(0);
            $table->decimal('nilai_kehadiran', 5, 2)->default(0);
            $table->decimal('nilai_administrasi', 5, 2)->default(0);
            $table->decimal('nilai_pembelajaran', 5, 2)->default(0);
            $table->decimal('nilai_akhir', 5, 2)->default(0);
            $table->enum('predikat', ['Sangat Baik', 'Baik', 'Cukup', 'Kurang'])->nullable();
            $table->text('catatan')->nullable();
            $table->foreignId('dievaluasi_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('dievaluasi_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluasi_kinerjas');
    }
};
