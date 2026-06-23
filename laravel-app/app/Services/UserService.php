<?php

namespace App\Services;

use App\Enums\UserRole;
use Illuminate\Support\Collection;

class UserService
{
    public function getRolesForSelect(): Collection
    {
        return collect(UserRole::cases())->map(fn ($role) => (object) [
            'id' => $role->value,
            'name' => $role->label(),
        ]);
    }
}
