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
        Schema::create('portales_agencia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aseguradora_id')->nullable()->constrained('aseguradoras')->onDelete('set null');
            $table->string('nombre');
            $table->string('usuario');
            $table->string('password');
            $table->string('link')->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('portales_agencia');
    }
};
