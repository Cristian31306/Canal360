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
        Schema::table('aseguradoras', function (Blueprint $table) {
            $table->dropColumn(['contacto_nombre', 'telefono', 'email', 'ejecutivo_asignado']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aseguradoras', function (Blueprint $table) {
            $table->string('contacto_nombre')->nullable();
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();
            $table->string('ejecutivo_asignado')->nullable();
        });
    }
};
