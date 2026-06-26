<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('apellido')->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->string('foto')->nullable();
            $table->string('telefono')->nullable();
            $table->string('pais')->default('PE');
            $table->string('moneda_predeterminada')->default('PEN');
            $table->string('idioma')->default('es');
            $table->string('tema')->default('light'); // light, dark
            $table->boolean('dos_fa_enabled')->default(false);
            $table->string('dos_fa_secret')->nullable();
            $table->string('contrasena_temporal')->nullable();
            $table->boolean('email_verificado')->default(false);
            $table->boolean('telefono_verificado')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'apellido',
                'fecha_nacimiento',
                'foto',
                'telefono',
                'pais',
                'moneda_predeterminada',
                'idioma',
                'tema',
                'dos_fa_enabled',
                'dos_fa_secret',
                'contrasena_temporal',
                'email_verificado',
                'telefono_verificado'
            ]);
        });
    }
};