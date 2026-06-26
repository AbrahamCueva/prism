<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Transaccion;

class TransaccionPolicy
{
    public function view(User $user, Transaccion $transaccion): bool
    {
        return $user->id === $transaccion->user_id;
    }

    public function update(User $user, Transaccion $transaccion): bool
    {
        return $user->id === $transaccion->user_id;
    }

    public function delete(User $user, Transaccion $transaccion): bool
    {
        return $user->id === $transaccion->user_id;
    }
}