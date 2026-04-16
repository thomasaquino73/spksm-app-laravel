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
        Schema::create('hastags', function (Blueprint $table) {
            $table->bigIncrements('tag_id');
            $table->unsignedBigInteger('tag_newsid');
            $table->string('tag_name', 100);
            $table->string('tag_slug', 120);
            $table->unsignedInteger('tag_click_count')->default(0);
            $table->integer('created_by')->nullable();
            $table->timestamps();
            $table->integer('updated_by')->nullable();
            $table->unique('tag_name');
            $table->unique('tag_slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hastags');
    }
};
