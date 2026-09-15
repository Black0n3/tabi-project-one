<?php

namespace App\Policies;

use App\Models\Building;
use App\Models\User;

class BuildingPolicy
{
    public function view(User $user, Building $building): bool
    {
        return $user->isAdmin() || $building->belongsToInvestor($user->investor?->id);
    }

    public function update(User $user, Building $building): bool
    {
        return $this->view($user, $building);
    }

    public function delete(User $user, Building $building): bool
    {
        return $this->view($user, $building);
    }
}
