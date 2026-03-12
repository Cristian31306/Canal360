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
        Schema::create('polizas', function (Blueprint $table) {
            $table->id();
            $table->string('numero_poliza')->unique();
            $table->foreignId('aseguradora_id')->constrained('aseguradoras')->onDelete('cascade');
            $table->foreignId('ramo_id')->constrained('ramos')->onDelete('cascade');
            $table->foreignId('riesgo_id')->constrained('riesgos')->onDelete('cascade');
            $table->date('expedicion_fecha');
            $table->date('inicio_vigencia');
            $table->date('fin_vigencia');
            $table->decimal('valor_asegurado', 15, 2);
            $table->decimal('prima_antes_iva', 15, 2);
            $table->decimal('iva', 15, 2);
            $table->decimal('prima_total', 15, 2);
            $table->decimal('tasa', 10, 6)->nullable();
            $table->enum('estado', ['vigente', 'vencida', 'renovada', 'cancelada'])->default('vigente');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('polizas');
    }
};
