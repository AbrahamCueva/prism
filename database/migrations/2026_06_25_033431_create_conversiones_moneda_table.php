<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('conversiones_moneda', function (Blueprint $table) {
            $table->id();
            $table->string('moneda_origen');
            $table->string('moneda_destino');
            $table->decimal('tasa_cambio', 10, 6);
            $table->timestamp('fecha_actualizacion')->useCurrent();
            $table->timestamps();

            $table->unique(['moneda_origen', 'moneda_destino']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversiones_moneda');
    }
};
