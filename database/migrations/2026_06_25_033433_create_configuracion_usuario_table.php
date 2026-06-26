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
        Schema::create('configuracion_usuario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->unique();
            $table->string('idioma')->default('es');
            $table->string('moneda_predeterminada')->default('PEN');
            $table->string('tema')->default('light'); // light, dark, auto
            $table->boolean('notificaciones_habilitadas')->default(true);
            $table->enum('privacidad_datos', ['publico', 'privado'])->default('privado');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuracion_usuario');
    }
};
