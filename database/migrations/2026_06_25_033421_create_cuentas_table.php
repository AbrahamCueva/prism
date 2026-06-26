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
        Schema::create('cuentas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nombre'); // "BCP Sueldo", "Yape", etc
            $table->enum('tipo', ['bancaria', 'billetera_digital', 'fondo', 'efectivo']);
            $table->string('moneda'); // PEN, USD, etc
            $table->decimal('saldo_actual', 15, 2)->default(0);
            $table->decimal('saldo_inicial', 15, 2)->default(0);
            $table->text('numero_cuenta')->nullable(); // Encriptado
            $table->string('banco')->nullable(); // BCP, BBVA, Yape, etc
            $table->string('color')->default('#3B82F6'); // Hex color para UI
            $table->boolean('activa')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cuentas');
    }
};
