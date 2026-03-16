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
        Schema::create('precio_minerales', function (Blueprint $table) {
            $table->id();
            $table->integer('mes');
            $table->integer('anio');
            $table->decimal('oro', 15, 2);
            $table->decimal('plata', 15, 2);
            $table->decimal('platino', 15, 2);
            $table->timestamps();
            
            $table->unique(['mes', 'anio']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('precio_minerales');
    }
};
