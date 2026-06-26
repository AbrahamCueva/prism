<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfiguracionUsuario extends Model
{
    use HasFactory;

    protected $table = 'configuracion_usuario';

    protected $fillable = [
        'user_id',
        'idioma',
        'moneda_predeterminada',
        'tema',
        'notificaciones_habilitadas',
        'privacidad_datos',
    ];

    protected $casts = [
        'notificaciones_habilitadas' => 'boolean',
    ];

    // Relaciones
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}