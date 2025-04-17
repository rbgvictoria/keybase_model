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
        Schema::create('change_notes', function (Blueprint $table) {
            $table->id();
            $table->timestampsTz();
            $table->text('remarks');
            $table->unsignedBigInteger('key_id');
            $table->unsignedBigInteger('version');
            $table->unsignedBigInteger('created_by_id');
            $table->unsignedBigInteger('updated_by_id')->nullable();
            $table->unique(['key_id', 'version']);
            $table->index('created_by_id');
            $table->index('updated_by_id');
            $table->foreign('key_id')->references('id')->on('keys');
            $table->foreign('created_by_id')->references('id')->on('agents');
            $table->foreign('updated_by_id')->references('id')->on('agents');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('change_notes');
    }
};
