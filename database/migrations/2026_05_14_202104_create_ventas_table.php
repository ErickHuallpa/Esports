<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes');
            $table->foreignId('pago_id')->constrained('pagos');
            $table->decimal('precio_total', 10, 2);
            $table->decimal('descuento_aplicado', 10, 2)->nullable();
            $table->enum('estado_venta', ['pendiente', 'confirmada', 'cancelada']);
            $table->timestamp('fecha_venta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};