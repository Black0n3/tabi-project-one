<?php

namespace App\Livewire\Public;

use App\Models\Room;
use App\Models\Unit;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class UnitPage extends Component
{
    public Unit $unit;

    public function mount(Unit $unit): void
    {
        abort_if($unit->building->project->is_hidden, 404);

        $this->unit = $unit;
    }

    public function render()
    {
        $this->unit->load('building.project.investor', 'floor');

        $rooms = $this->unit->rooms;

        return view('livewire.public.unit-page', [
            'planUrl' => $this->unit->floor_plan_image ? Storage::disk('public')->url($this->unit->floor_plan_image) : null,
            'roomsData' => $rooms->map(fn (Room $room) => [
                'id' => $room->id,
                'label' => $room->name,
                'area' => $room->area_m2 ? (float) $room->area_m2 : null,
                'points' => $room->polygon,
            ]),
        ])->layout('layouts.public', [
            'title' => $this->unit->building->project->name.' — '.$this->unit->code,
            'description' => $this->unit->description
                ?: "{$this->unit->type->label()} {$this->unit->code}, {$this->unit->area_m2} m², {$this->unit->status->label()}.",
            'image' => $this->unit->floor_plan_image ? Storage::disk('public')->url($this->unit->floor_plan_image) : null,
        ]);
    }
}
