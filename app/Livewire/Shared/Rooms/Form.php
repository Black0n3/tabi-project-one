<?php

namespace App\Livewire\Shared\Rooms;

use App\Livewire\Concerns\ResolvesPanelContext;
use App\Models\Room;
use App\Models\Unit;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Prostorija')]
class Form extends Component
{
    use ResolvesPanelContext;

    public ?Room $room = null;

    public ?Unit $unit = null;

    public string $name = '';

    public string $area_m2 = '';

    public function mount(?Room $room = null, ?int $unitId = null): void
    {
        $this->room = $room;

        if ($room) {
            $this->authorize('update', $room);

            $this->unit = $room->unit;
            $this->name = $room->name;
            $this->area_m2 = (string) $room->area_m2;
        } else {
            $this->unit = Unit::findOrFail($unitId ?? request()->integer('unit'));
            $this->authorize('update', $this->unit);
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

        $this->redirect(route($this->panelPrefix().'units.show', $this->unit), navigate: true);
    }

    public function render()
    {
        return view('livewire.shared.rooms.form', [
            'routePrefix' => $this->panelPrefix(),
        ])->layout($this->panelLayout());
    }
}
