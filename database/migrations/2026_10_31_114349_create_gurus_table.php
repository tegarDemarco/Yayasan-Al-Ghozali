<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gurus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();
            $table->string('nip')->nullable();
            $table->string('nuptk')->nullable();
            $table->enum('jenjang', ['SD', 'MTs', 'SMK']);
            $table->string('spesialisasi')->comment('Mata pelajaran utama');
            $table->enum('status_kepegawaian', ['PNS', 'GTY', 'GTT', 'Honorer'])->default('GTY');
            $table->string('pendidikan_terakhir')->nullable();
            $table->date('tanggal_bergabung')->nullable();
            $table->enum('status', ['aktif', 'nonaktif', 'cuti'])->default('aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gurus');
    }
};
