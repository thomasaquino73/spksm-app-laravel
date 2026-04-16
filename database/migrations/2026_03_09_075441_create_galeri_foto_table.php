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
        Schema::create('galeri_foto', function (Blueprint $table) {
            $table->id();
            $table->string('photo_folder')->nullable();
            $table->string('photo_filename')->nullable();
            $table->string('photo_thumbnail')->nullable();
            $table->string('photo_alias')->nullable();
            $table->string('kategori_berita_id')->nullable();
            $table->string('caption');
            $table->string('slug');
            $table->text('description');
            $table->text('publish_reason')->nullable();
            $table->string('keyword');
            $table->unsignedBigInteger('photographer_id');
            $table->tinyInteger('status')->default(1)->comment('0=delete, 1=draft, 2=unpublish, 3=publish, 4=scheduled');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->dateTime('publish_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('galeri_foto');
    }
};
