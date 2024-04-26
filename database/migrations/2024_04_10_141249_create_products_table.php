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
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            //$table->integer('IdProduct');
            $table->string('Name');
            $table->decimal('LeadTime',10,2)->nullable(); // Calcula cuanto se demora en llegar si pido hoy (númerico)
            $table->string('UnitMeasurement'); // unidad de medida para esa cantidad de stock

            // Crea atributo de llave foránea
            //$table->foreignId('IdArea')->constrained('areas')->cascadeOnUpdate()->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
