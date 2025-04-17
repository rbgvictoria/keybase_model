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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->timestampsTz();
            $table->string('node_name')->nullable();
            $table->text('lead_text');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->unsignedBigInteger('item_id')->nullable();
            $table->unsignedBigInteger('reticulation_id')->nullable();
            $table->unsignedBigInteger('subkey_id')->nullable();
            $table->unsignedBigInteger('key_id');
            $table->unsignedBigInteger('created_by_id')->nullable();
            $table->unsignedBigInteger('updated_by_id')->nullable();
            $table->index('parent_id');
            $table->index('item_id');
            $table->index('reticulation_id');
            $table->index('subkey_id');
            $table->index('key_id');
            $table->index('created_by_id');
            $table->index('updated_by_id');
            $table->foreign('parent_id')->references('id')->on('leads');
            $table->foreign('item_id')->references('id')->on('items');
            $table->foreign('reticulation_id')->references('id')->on('leads');
            $table->foreign('subkey_id')->references('id')->on('keys');
            $table->foreign('key_id')->references('id')->on('keys');
            $table->foreign('created_by_id')->references('id')->on('agents');
            $table->foreign('updated_by_id')->references('id')->on('agents');
        });

        Schema::table('keys', function (Blueprint $table) {
            $table->foreign('first_step_id')->references('id')->on('leads');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('keys', function (Blueprint $table) {
            $table->dropForeign('keys_first_step_id_foreign');
        });

        Schema::dropIfExists('leads');
    }
};
