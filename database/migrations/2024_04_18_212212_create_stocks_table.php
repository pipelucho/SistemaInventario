<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();

            //$table->integer('IdProduct');
            $table->boolean('IsActive')->default(true); // Sirve para desabilitar un producto, es decir, no pedir mas de ese True False
            $table->decimal('EstimatedDurability',10,2)->nullable();; // hasta cuando me alcanza si pido hoy (númerico)
            $table->string('Status')->nullable();//Sin stock - Se agotará - Pedir - Disponible
            $table->decimal('Quantity',10,2)->default(0); // cantidad en stock
            $table->string('UnitMeasurement'); // unidad de medida para esa cantidad de stock
            $table->foreignId('IdArea')->constrained('areas')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('IdProduct')->constrained('products')->cascadeOnUpdate()->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
