<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaccion extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'cuenta_origen_id',
        'cuenta_destino_id',
        'tipo',
        'categoria_id',
        'subcategoria_id',
        'moneda',
        'monto',
        'monto_convertido',
        'descripcion',
        'notas',
        'fecha_transaccion',
        'es_recurrente',
        'frecuencia',
        'proximo_pago',
        'comprobante_url',
        'estado',
    ];

    protected $casts = [
        'fecha_transaccion' => 'datetime',
        'proximo_pago' => 'datetime',
        'es_recurrente' => 'boolean',
        'monto' => 'decimal:2',
        'monto_convertido' => 'decimal:2',
    ];

    // Relaciones
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cuentaOrigen()
    {
        return $this->belongsTo(Cuenta::class, 'cuenta_origen_id');
    }

    public function cuentaDestino()
    {
        return $this->belongsTo(Cuenta::class, 'cuenta_destino_id');
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function subcategoria()
    {
        return $this->belongsTo(Subcategoria::class);
    }
}