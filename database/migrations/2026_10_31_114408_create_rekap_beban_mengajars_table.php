<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rekap_beban_mengajars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('gurus')->cascadeOnDelete();
            $table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();
            $table->string('tahun_ajaran');
            $table->enum('semester', ['Ganjil', 'Genap']);
            $table->integer('total_jam_mengajar')->default(0);
            $table->integer('jumlah_kelas')->default(0);
            $table->integer('jumlah_mapel')->default(0);
            $table->enum('status_beban', ['kurang', 'sesuai', 'lebih'])->default('sesuai');
            $table->integer('standar_jam_minimal')->default(24);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rekap_beban_mengajars');
    }
};
