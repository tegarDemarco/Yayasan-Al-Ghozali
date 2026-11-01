<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bahan_ajars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('gurus')->cascadeOnDelete();
            $table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();
            $table->string('judul');
            $table->string('mata_pelajaran');
            $table->string('kelas');
            $table->text('deskripsi')->nullable();
            $table->string('file_path');
            $table->string('tipe_file')->nullable();
            $table->bigInteger('ukuran_file')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bahan_ajars');
    }
};
