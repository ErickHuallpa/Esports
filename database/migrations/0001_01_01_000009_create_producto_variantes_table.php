<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('producto_variantes', function (Blueprint $table) {
            $table->id();
            // cascade asegura que si borras el producto, se borran sus variantes
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
            $table->string('talla', 50)->nullable();
            $table->string('color', 50)->nullable();
            $table->integer('stock')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('producto_variantes');
    }
};