<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('envios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_id')->constrained('ordenes');
            $table->text('direccion_destino')->nullable();
            $table->string('ciudad_destino', 100)->nullable();
            $table->string('zona_destino', 100)->nullable();
            $table->string('ruta', 150)->nullable();
            $table->enum('estado_envio', ['preparando', 'en camino', 'entregado', 'fallido']);
            $table->foreignId('admin_asignado')->nullable()->constrained('administradores');
            $table->string('codigo_seguimiento', 100)->nullable();
            $table->date('fecha_despacho')->nullable();
            $table->date('fecha_entrega_estimada')->nullable();
            $table->date('fecha_entrega_real')->nullable();
            $table->string('responsable_entrega', 150)->nullable();
            $table->decimal('costo_envio', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('envios');
    }
};