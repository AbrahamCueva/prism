<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cuenta extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nombre',
        'tipo',
        'moneda',
        'saldo_actual',
        'saldo_inicial',
        'numero_cuenta',
        'banco',
        'color',
        'activa',
    ];

    protected $casts = [
        'activa' => 'boolean',
        'saldo_actual' => 'decimal:2',
        'saldo_inicial' => 'decimal:2',
    ];

    // Relaciones
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transaccionesOrigen()
    {
        return $this->hasMany(Transaccion::class, 'cuenta_origen_id');
    }

    public function transaccionesDestino()
    {
        return $this->hasMany(Transaccion::class, 'cuenta_destino_id');
    }

    public function ahorros()
    {
        return $this->hasMany(Ahorro::class);
    }
}