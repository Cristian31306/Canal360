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
        Schema::create('abono_carteras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cartera_id')->constrained('carteras')->onDelete('cascade');
            $table->decimal('monto', 15, 2);
            $table->date('fecha_pago');
            $table->string('metodo_pago'); // efectivo, transferencia, consignacion, tarjeta
            $table->string('referencia')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('abono_carteras', function (Blueprint $table) {
            //
        });
    }
};
