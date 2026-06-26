<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ahorro extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nombre',
        'descripcion',
        'monto_objetivo',
        'monto_actual',
        'moneda',
        'cuenta_id',
        'fecha_inicio',
        'fecha_objetivo',
        'icono',
        'color',
        'estado',
        'progreso_porcentaje',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_objetivo' => 'date',
        'monto_objetivo' => 'decimal:2',
        'monto_actual' => 'decimal:2',
        'progreso_porcentaje' => 'decimal:2',
    ];

    // Relaciones
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cuenta()
    {
        return $this->belongsTo(Cuenta::class);
    }
}