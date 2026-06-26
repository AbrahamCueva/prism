<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Auditoria extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'accion',
        'tabla_afectada',
        'registro_id',
        'ip_address',
        'user_agent',
        'dispositivo',
        'pais',
        'ciudad',
    ];

    // Relaciones
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}