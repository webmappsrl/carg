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
        Schema::create('feature_collections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sheet_id')->nullable();
            $table->string('type'); //check tipo enum
            $table->string('geojson_path')->nullable();
            $table->foreign('sheet_id')->references('id')->on('sheets')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feature_collections');
    }
};
