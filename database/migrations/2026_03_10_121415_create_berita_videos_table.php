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
        Schema::create('berita_video', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('slug')->unique();
            $table->text('deskripsi')->nullable();
            $table->string('youtube_id');
            $table->string('keyword')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('kategori_berita_id')->nullable();
            $table->tinyInteger('status')->default(1)->comment('0=delete, 1=draft, 2=unpublish, 3=publish');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->dateTime('publish_date')->nullable();
            $table->text('publish_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('berita_video');
    }
};
