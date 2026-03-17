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
        Schema::create('titulos_360', function (Blueprint $table) {
            $table->id();
            $table->string('par')->nullable();
            $table->string('titulo')->unique();
            $table->string('nombre');
            $table->text('minerales')->nullable();
            $table->string('departamento')->nullable();
            $table->string('municipio')->nullable();
            $table->string('etapa')->nullable();
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->foreignId('aseguradora_id')->nullable()->constrained('aseguradoras')->nullOnDelete();
            $table->string('aseguradora_nombre')->nullable();
            $table->decimal('valor_asegurado', 15, 2)->default(0);
            $table->text('correo')->nullable();
            $table->text('celular')->nullable();
            $table->boolean('cliente_canal')->default(false);
            $table->text('asesores')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('titulos_360');
    }
};
