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
        Schema::create('ahorros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nombre'); // "Viaje a Cusco", etc
            $table->text('descripcion')->nullable();
            $table->decimal('monto_objetivo', 15, 2);
            $table->decimal('monto_actual', 15, 2)->default(0);
            $table->string('moneda');
            $table->foreignId('cuenta_id')->constrained('cuentas');
            $table->date('fecha_inicio');
            $table->date('fecha_objetivo');
            $table->string('icono')->default('🎯');
            $table->string('color')->default('#3B82F6');
            $table->enum('estado', ['activa', 'completada', 'cancelada'])->default('activa');
            $table->decimal('progreso_porcentaje', 5, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ahorros');
    }
};
