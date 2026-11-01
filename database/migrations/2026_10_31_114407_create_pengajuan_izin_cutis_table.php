<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan_izin_cutis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('gurus')->cascadeOnDelete();
            $table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();
            $table->enum('jenis_pengajuan', ['Izin', 'Cuti', 'Sakit', 'Tugas Luar', 'Dinas']);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->integer('jumlah_hari')->default(1);
            $table->text('alasan');
            $table->string('lampiran')->nullable();
            $table->enum('status_approval', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu');
            $table->text('catatan_approval')->nullable();
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('disetujui_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_izin_cutis');
    }
};
