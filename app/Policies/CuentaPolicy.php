<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Cuenta;

class CuentaPolicy
{
    public function view(User $user, Cuenta $cuenta): bool
    {
        return $user->id === $cuenta->user_id;
    }

    public function update(User $user, Cuenta $cuenta): bool
    {
        return $user->id === $cuenta->user_id;
    }

    public function delete(User $user, Cuenta $cuenta): bool
    {
        return $user->id === $cuenta->user_id;
    }
}