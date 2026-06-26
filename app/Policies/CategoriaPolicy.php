<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Categoria;

class CategoriaPolicy
{
    public function view(User $user, Categoria $categoria): bool
    {
        return $user->id === $categoria->user_id;
    }

    public function update(User $user, Categoria $categoria): bool
    {
        return $user->id === $categoria->user_id;
    }

    public function delete(User $user, Categoria $categoria): bool
    {
        return $user->id === $categoria->user_id;
    }
}