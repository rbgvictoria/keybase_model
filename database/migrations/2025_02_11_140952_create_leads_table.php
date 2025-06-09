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
            $table->text('statement');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->unsignedBigInteger('subkey_id')->nullable();
            $table->unsignedBigInteger('key_id');
            $table->unsignedBigInteger('created_by_id')->nullable();
            $table->unsignedBigInteger('updated_by_id')->nullable();
            $table->index('parent_id');
            $table->index('subkey_id');
            $table->index('key_id');
            $table->index('created_by_id');
            $table->index('updated_by_id');
            $table->foreign('parent_id')->references('id')->on('leads');
            $table->foreign('subkey_id')->references('id')->on('keys');
            $table->foreign('key_id')->references('id')->on('keys');
            $table->foreign('created_by_id')->references('id')->on('agents');
            $table->foreign('updated_by_id')->references('id')->on('agents');
        });

        Schema::create('lead_item', function(Blueprint $table) {
            $table->unsignedBigInteger('lead_id');
            $table->unsignedBigInteger('item_id');
            $table->unique(['lead_id', 'item_id']);
            $table->foreign('lead_id')->references('id')->on('leads');
            $table->foreign('item_id')->references('id')->on('items');
        });

        Schema::table('keys', function (Blueprint $table) {
            $table->foreign('root_id')->references('id')->on('leads');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('keys', function (Blueprint $table) {
            $table->dropForeign('keys_root_id_foreign');
        });

        Schema::dropIfExists('lead_item');
        Schema::dropIfExists('leads');
    }
};
