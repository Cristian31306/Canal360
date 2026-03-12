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
        Schema::dropIfExists('cliente_riesgo');
        Schema::create('cliente_riesgo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained()->cascadeOnDelete();
            $table->foreignId('riesgo_id')->constrained('riesgos')->cascadeOnDelete();
            $table->timestamps();
        });

        // Migrar datos existentes (si los hay)
        \Illuminate\Support\Facades\DB::statement('INSERT INTO cliente_riesgo (cliente_id, riesgo_id, created_at, updated_at) SELECT propietario_id, id, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP FROM riesgos');

        Schema::table('riesgos', function (Blueprint $table) {
            $table->dropForeign(['propietario_id']);
            $table->dropColumn('propietario_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('riesgos', function (Blueprint $table) {
            $table->foreignId('propietario_id')->nullable()->constrained('clientes')->onDelete('cascade');
        });

        Schema::dropIfExists('cliente_riesgo');
    }
};
