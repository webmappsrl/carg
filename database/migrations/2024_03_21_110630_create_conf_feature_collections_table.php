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
        Schema::create('conf_feature_collections', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('type');
            $table->string('label')->nullable();
            $table->string('svg_path')->nullable();
            $table->string('stroke_color')->nullable();
            $table->integer('stroke_width')->nullable()->default(2);
            $table->string('fill_color')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conf_feature_collections');
    }
};
