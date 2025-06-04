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
        Schema::create('keys', function (Blueprint $table) {
            $table->id();
            $table->timestampsTz();
            $table->uuid('guid');
            $table->unsignedBigInteger('version');
            $table->string('title');
            $table->string('author')->nullable();
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('root_id');
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('subkey_of_id')->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->unsignedBigInteger('created_by_id')->nullable();
            $table->unsignedBigInteger('updated_by_id')->nullable();
            $table->index('guid');
            $table->index('title');
            $table->index('project_id');
            $table->index('subkey_of_id');
            $table->index('item_id');
            $table->index('created_by_id');
            $table->index('updated_by_id');
            $table->foreign('item_id')->references('id')->on('items');
            $table->foreign('project_id')->references('id')->on('projects');
            $table->foreign('subkey_of_id')->references('id')->on('keys');
            $table->foreign('created_by_id')->references('id')->on('agents');
            $table->foreign('updated_by_id')->references('id')->on('agents');
        });

        Schema::create('key_item', function(Blueprint $table) {
            $table->unsignedBigInteger('key_id');
            $table->unsignedBigInteger('item_id');
            $table->unique(['key_id', 'item_id']);
            $table->foreign('key_id')->references('id')->on('keys');
            $table->foreign('item_id')->references('id')->on('items');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('key_item');
        Schema::dropIfExists('keys');
    }
};
