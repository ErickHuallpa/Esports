<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tipo_pago_id')->constrained('tipo_pagos');
            $table->foreignId('cliente_id')->constrained('clientes');
            $table->decimal('monto', 10, 2);
            $table->enum('estado', ['pendiente', 'verificado', 'rechazado']);
            $table->text('comprobante_url')->nullable();
            $table->text('motivo_rechazo')->nullable();
            $table->timestamp('fecha_pago')->nullable();
            $table->foreignId('verificado_por')->nullable()->constrained('administradores');
            $table->timestamp('fecha_verificacion')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};