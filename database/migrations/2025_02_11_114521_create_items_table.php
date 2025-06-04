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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->timestampsTz();
            $table->uuid('guid');
            $table->string('name');
            $table->string('url')->nullable();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('created_by_id')->nullable();
            $table->unsignedBigInteger('updated_by_id')->nullable();
            $table->index('name');
            $table->index('project_id');
            $table->index('created_by_id');
            $table->index('updated_by_id');
            $table->foreign('project_id')->references('id')->on('projects');
            $table->foreign('created_by_id')->references('id')->on('agents');
            $table->foreign('updated_by_id')->references('id')->on('agents');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->foreign('item_id')->references('id')->on('items');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign('projects_item_id_foreign');
        });

        Schema::dropIfExists('key_item');
        Schema::dropIfExists('lead_item');
        Schema::dropIfExists('items');
    }
};
