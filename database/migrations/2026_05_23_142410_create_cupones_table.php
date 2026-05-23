<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cupones', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->enum('tipo', ['porcentaje', 'fijo'])->default('fijo');
            $table->decimal('valor', 10, 2);
            $table->boolean('usado')->default(false);
            $table->timestamp('usado_en')->nullable();
            $table->unsignedBigInteger('usado_por')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cupones');
    }
};