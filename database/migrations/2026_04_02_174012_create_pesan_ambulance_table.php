<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pesan_ambulance', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pesan')->unique();
            $table->unsignedBigInteger('user_id');
            $table->string('nama_pasien');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('alamat_penjemputan');
            $table->dateTime('waktu_pesan');
            $table->tinyInteger('kondisi_pasien')->comment('0=sakit, 1=meninggal');
            $table->tinyInteger('lokasi_pengantaran')->comment('1=rumah, 2=rumah sakit, 3=rumah duka');
            $table->string('catatan_singkat', 255)->nullable();
            $table->enum('status', ['pending', 'diterima', 'ditolak','selesai','batal'])->default('pending');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesan_ambulance');
    }
};
