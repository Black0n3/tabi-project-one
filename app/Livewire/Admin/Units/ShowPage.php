<?php

namespace App\Livewire\Admin\Units;

use App\Models\Room;
use App\Models\Unit;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Jedinica')]
class ShowPage extends Component
{
    public Unit $unit;

    public function mount(Unit $unit): void
    {
        $this->unit = $unit;
    }

    public function deleteRoom(Room $room): void
    {
        $room->delete();

        session()->flash('status', 'Prostorija "'.$room->name.'" je obrisana.');
    }

    public function render()
    {
        $this->unit->load('building.project.investor', 'floor');

        $rooms = $this->unit->rooms;

        return view('livewire.admin.units.show-page', [
            'rooms' => $rooms,
        ]);
    }
}
