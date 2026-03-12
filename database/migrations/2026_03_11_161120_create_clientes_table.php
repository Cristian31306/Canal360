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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo_persona', ['natural', 'juridica']);
            $table->string('tipo_documento')->nullable();
            $table->string('numero_documento')->unique();
            $table->string('nombre_razon_social');
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();
            $table->string('direccion')->nullable();
            $table->string('ciudad')->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->date('fecha_contacto')->nullable();
            $table->text('observaciones')->nullable();
            $table->string('rep_legal_nombre')->nullable();
            $table->string('rep_legal_documento')->nullable();
            $table->string('rep_legal_telefono')->nullable();
            $table->string('rep_legal_email')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
