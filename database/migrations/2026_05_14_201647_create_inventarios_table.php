<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventarios', function (Blueprint $table) {
            $table->id();
            
            // El inventario ahora rastrea el movimiento de la variante específica (Talla/Color)
            $table->foreignId('producto_variante_id')->constrained('producto_variantes')->onDelete('cascade');
            
            // Apuntamos a la tabla users (el usuario que registra el movimiento)
            $table->foreignId('user_id')->constrained('users');
            
            $table->enum('tipo_movimiento', ['entrada', 'salida', 'ajuste']);
            $table->integer('cantidad');
            $table->integer('stock_anterior');
            $table->integer('stock_resultante');
            $table->text('motivo')->nullable();
            $table->timestamp('fecha_movimiento')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventarios');
    }
};