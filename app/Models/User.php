<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable([
    'name',
    'email',
    'password',
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
    'telefono_verificado',
])]
#[Hidden([
    'password',
    'remember_token',
    'dos_fa_secret',
])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'fecha_nacimiento' => 'date',
            'dos_fa_enabled' => 'boolean',
            'email_verificado' => 'boolean',
            'telefono_verificado' => 'boolean',
        ];
    }

    // Relaciones
    public function cuentas()
    {
        return $this->hasMany(Cuenta::class);
    }

    public function categorias()
    {
        return $this->hasMany(Categoria::class);
    }

    public function transacciones()
    {
        return $this->hasMany(Transaccion::class);
    }

    public function ahorros()
    {
        return $this->hasMany(Ahorro::class);
    }

    public function configuracion()
    {
        return $this->hasOne(ConfiguracionUsuario::class);
    }

    public function auditorias()
    {
        return $this->hasMany(Auditoria::class);
    }
}
