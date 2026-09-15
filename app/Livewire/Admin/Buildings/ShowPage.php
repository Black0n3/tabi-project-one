<?php

namespace App\Livewire\Admin\Buildings;

use App\Models\Building;
use App\Models\Floor;
use App\Models\Unit;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Objekat')]
class ShowPage extends Component
{
    public Building $building;

    public function mount(Building $building): void
    {
        $this->building = $building;
    }

    public function deleteFloor(Floor $floor): void
    {
        $floor->delete();

        session()->flash('status', 'Kat "'.$floor->label.'" je obrisan (jedinice na tom katu su zadržane, ali su otkačene s kata).');
    }

    public function deleteUnit(Unit $unit): void
    {
        $unit->delete();

        session()->flash('status', 'Jedinica "'.$unit->code.'" je obrisana.');
    }

    public function render()
    {
        $this->building->load('project.investor');

        $floors = $this->building->floors()->withCount('units')->get();
        $units = $this->building->units()->with('floor')->orderBy('code')->get();

        return view('livewire.admin.buildings.show-page', [
            'floors' => $floors,
            'units' => $units,
        ]);
    }
}
