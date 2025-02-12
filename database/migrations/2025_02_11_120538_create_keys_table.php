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
            $table->integer('version');
            $table->string('title');
            $table->string('author')->nullable();
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('modified_from_source')->nullable();
            $table->integer('first_step_id');
            $table->integer('taxonomic_scope_id');
            $table->integer('project_id');
            $table->integer('subkey_of_id')->nullable();
            $table->integer('source_id')->nullable();
            $table->integer('created_by_id')->nullable();
            $table->integer('updated_by_id')->nullable();
            $table->index('guid');
            $table->index('title');
            $table->index('project_id');
            $table->index('subkey_of_id');
            $table->index('taxonomic_scope_id');
            $table->index('created_by_id');
            $table->index('updated_by_id');
            $table->foreign('taxonomic_scope_id')->references('id')->on('items');
            $table->foreign('project_id')->references('id')->on('projects');
            $table->foreign('subkey_of_id')->references('id')->on('keys');
            $table->foreign('created_by_id')->references('id')->on('agents');
            $table->foreign('updated_by_id')->references('id')->on('agents');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keys');
    }
};
