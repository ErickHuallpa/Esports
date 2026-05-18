<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detalle_ventas', function (Blueprint $table) {
            $table->id();
            
            // Relación con la venta general (si se borra la venta, se borra el detalle)
            $table->foreignId('venta_id')->constrained('ventas')->onDelete('cascade');
            
            // CAMBIO CRUCIAL: Apuntamos a la variante exacta (Talla, Color)
            $table->foreignId('producto_variante_id')->constrained('producto_variantes');
            
            $table->integer('cantidad');
            
            // Estos precios se copian aquí en el momento de la compra para mantener un registro histórico. 
            // Si el precio del producto cambia mañana, esta venta pasada no se verá afectada.
            $table->decimal('precio_unitario_compra', 10, 2);
            $table->decimal('precio_unitario_venta', 10, 2);
            
            $table->decimal('descuento_unitario', 10, 2)->default(0);
            $table->decimal('subtotal', 10, 2);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalle_ventas');
    }
};