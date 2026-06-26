<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConversionMoneda extends Model
{
    use HasFactory;

    protected $fillable = [
        'moneda_origen',
        'moneda_destino',
        'tasa_cambio',
        'fecha_actualizacion',
    ];

    protected $casts = [
        'tasa_cambio' => 'decimal:6',
        'fecha_actualizacion' => 'datetime',
    ];
}