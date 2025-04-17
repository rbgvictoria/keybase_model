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
        Schema::create('filters', function (Blueprint $table) {
            $table->id();
            $table->timestampsTz();
            $table->string('title');
            $table->longText('items_not_found')->nullable();
            $table->boolean('is_project_filter')->nullable();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('created_by_id')->nullable();
            $table->unsignedBigInteger('updated_by_id')->nullable();
            $table->index('project_id');
            $table->index('created_by_id');
            $table->index('updated_by_id');
            $table->foreign('project_id')->references('id')->on('projects');
            $table->foreign('created_by_id')->references('id')->on('agents');
            $table->foreign('updated_by_id')->references('id')->on('agents');
        });

        Schema::create('filter_item', function(Blueprint $table) {
            $table->unsignedBigInteger('filter_id');
            $table->unsignedBigInteger('item_id');
            $table->unique(['filter_id', 'item_id']);
            $table->foreign('filter_id')->references('id')->on('filters');
            $table->foreign('item_id')->references('id')->on('items');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('filter_item');
        
        Schema::dropIfExists('filters');
    }
};
