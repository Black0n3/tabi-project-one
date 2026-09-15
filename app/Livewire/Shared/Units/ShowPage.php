<?php

namespace App\Livewire\Shared\Units;

use App\Livewire\Concerns\ResolvesPanelContext;
use App\Models\Room;
use App\Models\Unit;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Jedinica')]
class ShowPage extends Component
{
    use ResolvesPanelContext;

    public Unit $unit;

    public function mount(Unit $unit): void
    {
        $this->authorize('view', $unit);

        $this->unit = $unit;
    }

    public function deleteRoom(Room $room): void
    {
        $this->authorize('delete', $room);

        $room->delete();

        session()->flash('status', 'Prostorija "'.$room->name.'" je obrisana.');
    }

    public function render()
    {
        $this->unit->load('building.project.investor', 'floor');

        $rooms = $this->unit->rooms;

        return view('livewire.shared.units.show-page', [
            'rooms' => $rooms,
            'routePrefix' => $this->panelPrefix(),
            'investorUrl' => $this->investorHomeUrl($this->unit->building->project->investor),
        ])->layout($this->panelLayout());
    }
}
