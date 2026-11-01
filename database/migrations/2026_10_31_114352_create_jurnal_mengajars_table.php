<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jurnal_mengajars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('gurus')->cascadeOnDelete();
            $table->foreignId('jadwal_id')->nullable()->constrained('jadwal_mengajars')->nullOnDelete();
            $table->date('tanggal');
            $table->string('mata_pelajaran');
            $table->string('kelas');
            $table->string('pertemuan_ke')->nullable();
            $table->text('materi');
            $table->text('kegiatan')->nullable();
            $table->integer('jumlah_siswa_hadir')->default(0);
            $table->text('catatan')->nullable();
            $table->enum('status', ['draft', 'terkirim'])->default('draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jurnal_mengajars');
    }
};
