<?php

namespace App\Livewire\Admin\Floors;

use App\Models\Building;
use App\Models\Floor;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.admin')]
#[Title('Kat')]
class Form extends Component
{
    use WithFileUploads;

    public ?Floor $floor = null;

    public ?Building $building = null;

    public string $label = '';

    public int $order = 0;

    public $floor_plan_image = null;

    public function mount(?Floor $floor = null, ?int $buildingId = null): void
    {
        $this->floor = $floor;

        if ($floor) {
            $this->building = $floor->building;
            $this->label = $floor->label;
            $this->order = $floor->order;
        } else {
            $this->building = Building::findOrFail($buildingId ?? request()->integer('building'));
            $this->order = $this->building->floors()->count();
        }
    }

    protected function rules(): array
    {
        return [
            'label' => ['required', 'string', 'max:255'],
            'order' => ['required', 'integer', 'min:0'],
            'floor_plan_image' => ['nullable', 'image', 'max:4096'],
        ];
    }

    public function save(): void
    {
        $validated = $this->validate();

        $floor = $this->floor ?? new Floor(['building_id' => $this->building->id]);
        $floor->fill($validated);

        if ($this->floor_plan_image) {
            $floor->floor_plan_image = $this->floor_plan_image->store('floors/plans', 'public');
        }

        $floor->save();

        session()->flash('status', 'Kat je spremljen.');

        $this->redirect(route('admin.buildings.show', $this->building), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.floors.form');
    }
}
