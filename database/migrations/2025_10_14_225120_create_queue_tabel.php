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
        Schema::create('queue', function (Blueprint $table) {
            $table->id('queue_id');
            $table->string('queue_name');
            $table->string('queue_tablename');
            $table->text('queue_processdata');
            $table->dateTime('queue_processdate');
            $table->string('create_by', 100);
            $table->dateTime('create_at');
            $table->string('update_by', 100)->nullable();
            $table->dateTime('update_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('bjdb')->dropIfExists('queue');
    }
};
