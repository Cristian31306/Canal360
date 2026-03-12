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
        Schema::table('riesgos', function (Blueprint $table) {
            $table->dropColumn('metadatos');
            $table->text('descripcion')->nullable()->after('identificador');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('riesgos', function (Blueprint $table) {
            $table->dropColumn('descripcion');
            $table->json('metadatos')->nullable()->after('identificador');
        });
    }
};
