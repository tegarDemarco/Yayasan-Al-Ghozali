<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sk_tugas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('gurus')->cascadeOnDelete();
            $table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();
            $table->string('no_sk');
            $table->string('judul_sk');
            $table->enum('jenis_sk', ['Pengangkatan', 'Pemberhentian', 'Mutasi', 'Penghargaan', 'Sanksi', 'Tugas Tambahan']);
            $table->date('tanggal_sk');
            $table->date('berlaku_mulai')->nullable();
            $table->date('berlaku_sampai')->nullable();
            $table->string('file_path')->nullable();
            $table->text('keterangan')->nullable();
            $table->foreignId('diterbitkan_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sk_tugas');
    }
};
