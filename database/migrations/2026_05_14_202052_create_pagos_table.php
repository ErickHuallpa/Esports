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
            
            // Apunta al usuario que compró (antes cliente_id)
            $table->foreignId('user_id')->constrained('users'); 
            
            $table->decimal('monto', 10, 2);
            $table->enum('estado', ['pendiente', 'verificado', 'rechazado']);
            $table->text('comprobante_url')->nullable();
            
            // Vital para la comunicación en pagos rechazados
            $table->text('motivo_rechazo')->nullable(); 
            
            $table->timestamp('fecha_pago')->nullable();
            
            // Apunta al usuario admin que verificó (antes administradors)
            $table->foreignId('verificado_por')->nullable()->constrained('users'); 
            
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