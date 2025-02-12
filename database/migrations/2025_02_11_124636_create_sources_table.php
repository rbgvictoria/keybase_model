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
        Schema::create('sources', function (Blueprint $table) {
            $table->id();
            $table->timestampsTz();
            $table->string('title');
            $table->string('authors')->nullable();
            $table->string('year', 16)->nullable();
            $table->string('in_authors')->nullable();
            $table->string('in_title')->nullable();
            $table->string('edition', 32)->nullable();
            $table->string('journal')->nullable();
            $table->string('series', 64)->nullable();
            $table->string('volume', 64)->nullable();
            $table->string('issue', 64)->nullable();
            $table->string('part', 64)->nullable();
            $table->string('publisher')->nullable();
            $table->string('place_of_publication')->nullable();
            $table->string('pages', 32)->nullable();
            $table->string('url')->nullable();
            $table->integer('project_id');
            $table->integer('created_by_id')->nullable();
            $table->integer('updated_by_id')->nullable();
            $table->index('title');
            $table->index('authors');
            $table->index('year');
            $table->index('project_id');
            $table->index('created_by_id');
            $table->index('updated_by_id');
            $table->foreign('project_id')->references('id')->on('projects');
            $table->foreign('created_by_id')->references('id')->on('agents');
            $table->foreign('updated_by_id')->references('id')->on('agents');
        });

        Schema::table('keys', function (Blueprint $table) {
            $table->foreign('source_id')->references('id')->on('sources');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('keys', function (Blueprint $table) {
            $table->dropForeign('keys_source_id_foreign');
        });

        Schema::dropIfExists('sources');
    }
};
