<?php

namespace App\Policies;

use App\Models\Room;
use App\Models\User;

class RoomPolicy
{
    public function view(User $user, Room $room): bool
    {
        return $user->isAdmin() || $room->belongsToInvestor($user->investor?->id);
    }

    public function update(User $user, Room $room): bool
    {
        return $this->view($user, $room);
    }

    public function delete(User $user, Room $room): bool
    {
        return $this->view($user, $room);
    }
}
