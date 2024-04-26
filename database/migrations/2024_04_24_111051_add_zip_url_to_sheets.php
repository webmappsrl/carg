<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('sheets', function (Blueprint $table) {
            $table->string('file')->nullable(); // Aggiungi una colonna per l'URL dello ZIP
        });
    }

    public function down()
    {
        Schema::table('sheets', function (Blueprint $table) {
            $table->dropColumn('file'); // Rimuovi la colonna
        });
    }
};
