<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resenas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes');
            $table->foreignId('producto_id')->constrained('productos');
            $table->tinyInteger('calificacion');
            $table->text('comentario')->nullable();
            $table->timestamp('fecha_resena')->useCurrent();
            $table->timestamps();

            $table->unique(['cliente_id', 'producto_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resenas');
    }
};