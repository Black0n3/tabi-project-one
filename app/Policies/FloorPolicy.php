<?php

namespace App\Policies;

use App\Models\Floor;
use App\Models\User;

class FloorPolicy
{
    public function view(User $user, Floor $floor): bool
    {
        return $user->isAdmin() || $floor->belongsToInvestor($user->investor?->id);
    }

    public function update(User $user, Floor $floor): bool
    {
        return $this->view($user, $floor);
    }

    public function delete(User $user, Floor $floor): bool
    {
        return $this->view($user, $floor);
    }
}
