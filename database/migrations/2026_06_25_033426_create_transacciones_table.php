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
        Schema::create('transacciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('cuenta_origen_id')->nullable()->constrained('cuentas');
            $table->foreignId('cuenta_destino_id')->nullable()->constrained('cuentas');
            $table->enum('tipo', ['ingreso', 'gasto', 'transferencia', 'deposito', 'retiro']);
            $table->foreignId('categoria_id')->nullable()->constrained();
            $table->foreignId('subcategoria_id')->nullable()->constrained();
            $table->string('moneda');
            $table->decimal('monto', 15, 2);
            $table->decimal('monto_convertido', 15, 2)->nullable();
            $table->text('descripcion')->nullable();
            $table->text('notas')->nullable();
            $table->dateTime('fecha_transaccion');
            $table->boolean('es_recurrente')->default(false);
            $table->string('frecuencia')->nullable(); // diaria, semanal, mensual
            $table->dateTime('proximo_pago')->nullable();
            $table->string('comprobante_url')->nullable();
            $table->enum('estado', ['pendiente', 'completada', 'fallida'])->default('completada');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transacciones');
    }
};
