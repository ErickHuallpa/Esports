<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ordenes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->constrained('ventas')->onDelete('cascade');
            
            // Apunta al usuario receptor
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            $table->string('estado_orden', 50); // Ej: Preparando, En tránsito, Entregada
            
            // Campos logísticos esenciales para el ruteo de entregas
            $table->string('ciudad_destino', 100)->nullable(); 
            $table->text('direccion_envio')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ordenes');
    }
};