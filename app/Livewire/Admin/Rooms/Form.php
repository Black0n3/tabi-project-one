<?php

namespace App\Livewire\Admin\Rooms;

use App\Models\Room;
use App\Models\Unit;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Prostorija')]
class Form extends Component
{
    public ?Room $room = null;

    public ?Unit $unit = null;

    public string $name = '';

    public string $area_m2 = '';

    public function mount(?Room $room = null, ?int $unitId = null): void
    {
        $this->room = $room;

        if ($room) {
            $this->unit = $room->unit;
            $this->name = $room->name;
            $this->area_m2 = (string) $room->area_m2;
        } else {
            $this->unit = Unit::findOrFail($unitId ?? request()->integer('unit'));
        }
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'area_m2' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function save(): void
    {
        $validated = $this->validate();
        $validated['area_m2'] = $validated['area_m2'] ?: null;

        $room = $this->room ?? new Room(['unit_id' => $this->unit->id]);
        $room->fill($validated);
        $room->save();

        session()->flash('status', 'Prostorija je spremljena.');

        $this->redirect(route('admin.units.show', $this->unit), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.rooms.form');
    }
}
