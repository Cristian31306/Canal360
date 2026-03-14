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
        Schema::table('polizas', function (Blueprint $table) {
            // Forzamos el cambio de estado a text para evitar errores de enum en SQLite durante migraciones complejas
            $table->string('estado')->default('vigente')->change();
            
            $table->text('liquidacion')->nullable()->after('estado');
            $table->foreignId('poliza_anterior_id')->nullable()->after('liquidacion')->constrained('polizas')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('polizas', function (Blueprint $table) {
            $table->dropForeign(['poliza_anterior_id']);
            $table->dropColumn(['liquidacion', 'poliza_anterior_id']);
            
            $table->enum('estado', ['vigente', 'vencida', 'renovada', 'cancelada'])
                  ->default('vigente')
                  ->change();
        });
    }
};
