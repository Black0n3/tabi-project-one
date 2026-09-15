<?php

namespace App\Livewire\Shared\Buildings;

use App\Livewire\Concerns\ResolvesPanelContext;
use App\Models\Building;
use App\Models\Floor;
use App\Models\Unit;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Objekat')]
class ShowPage extends Component
{
    use ResolvesPanelContext;

    public Building $building;

    public function mount(Building $building): void
    {
        $this->authorize('view', $building);

        $this->building = $building;
    }

    public function deleteFloor(Floor $floor): void
    {
        $this->authorize('delete', $floor);

        $floor->delete();

        session()->flash('status', 'Kat "'.$floor->label.'" je obrisan (jedinice na tom katu su zadržane, ali su otkačene s kata).');
    }

    public function deleteUnit(Unit $unit): void
    {
        $this->authorize('delete', $unit);

        $unit->delete();

        session()->flash('status', 'Jedinica "'.$unit->code.'" je obrisana.');
    }

    public function render()
    {
        $this->building->load('project.investor');

        $floors = $this->building->floors()->withCount('units')->get();
        $units = $this->building->units()->with('floor')->orderBy('code')->get();

        return view('livewire.shared.buildings.show-page', [
            'floors' => $floors,
            'units' => $units,
            'routePrefix' => $this->panelPrefix(),
            'investorUrl' => $this->investorHomeUrl($this->building->project->investor),
        ])->layout($this->panelLayout());
    }
}
