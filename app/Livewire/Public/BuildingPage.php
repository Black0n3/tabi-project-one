<?php

namespace App\Livewire\Public;

use App\Models\Building;
use App\Models\Floor;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class BuildingPage extends Component
{
    public Building $building;

    public function mount(Building $building): void
    {
        $this->building = $building;
    }

    public function render()
    {
        $this->building->load('project.investor');

        $floors = $this->building->floors()
            ->with(['units' => fn ($query) => $query->orderBy('code')])
            ->get();

        $unassignedUnits = $this->building->units()
            ->whereNull('floor_id')
            ->orderBy('code')
            ->get();

        return view('livewire.public.building-page', [
            'facadeUrl' => $this->building->facade_image ? Storage::disk('public')->url($this->building->facade_image) : null,
            'floorsData' => $floors->map(fn (Floor $floor) => [
                'id' => $floor->id,
                'label' => $floor->label,
                'points' => $floor->polygon,
                'units' => $floor->units->map(fn ($unit) => [
                    'id' => $unit->id,
                    'code' => $unit->code,
                    'area' => (float) $unit->area_m2,
                    'price' => $unit->price ? (float) $unit->price : null,
                    'status' => $unit->status->value,
                    'statusLabel' => $unit->status->label(),
                    'url' => route('public.units.show', $unit),
                ]),
            ]),
            'unassignedUnits' => $unassignedUnits,
        ])->layout('layouts.public', ['title' => $this->building->name]);
    }
}
