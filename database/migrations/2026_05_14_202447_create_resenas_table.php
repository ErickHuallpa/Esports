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
            
            // Apunta al usuario (antes cliente_id)
            $table->foreignId('user_id')->constrained('users'); 
            
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
            $table->tinyInteger('calificacion');
            $table->text('comentario')->nullable();
            $table->timestamp('fecha_resena')->useCurrent();
            $table->timestamps();

            // Regla estricta: Un usuario solo puede dejar una reseña por producto
            $table->unique(['user_id', 'producto_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resenas');
    }
};