<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Ahorro;

class AhorroPolicy
{
    public function view(User $user, Ahorro $ahorro): bool
    {
        return $user->id === $ahorro->user_id;
    }

    public function update(User $user, Ahorro $ahorro): bool
    {
        return $user->id === $ahorro->user_id;
    }

    public function delete(User $user, Ahorro $ahorro): bool
    {
        return $user->id === $ahorro->user_id;
    }
}