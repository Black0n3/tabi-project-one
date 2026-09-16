<?php

namespace App\Livewire\Shared\Floors;

use App\Livewire\Concerns\ResolvesPanelContext;
use App\Models\Building;
use App\Models\Floor;
use App\Support\ImageUploads;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('Kat')]
class Form extends Component
{
    use ResolvesPanelContext, WithFileUploads;

    public ?Floor $floor = null;

    public ?Building $building = null;

    public string $label = '';

    public int $order = 0;

    public $floor_plan_image = null;

    public function mount(?Floor $floor = null, ?int $buildingId = null): void
    {
        $this->floor = $floor;

        if ($floor) {
            $this->authorize('update', $floor);

            $this->building = $floor->building;
            $this->label = $floor->label;
            $this->order = $floor->order;
        } else {
            $this->building = Building::findOrFail($buildingId ?? request()->integer('building'));
            $this->authorize('update', $this->building);
            $this->order = $this->building->floors()->count();
        }
    }

    protected function rules(): array
    {
        return [
            'label' => ['required', 'string', 'max:255'],
            'order' => ['required', 'integer', 'min:0'],
            'floor_plan_image' => ['nullable', 'image', 'max:8192'],
        ];
    }

    public function save(): void
    {
        $validated = $this->validate();

        $floor = $this->floor ?? new Floor(['building_id' => $this->building->id]);
        $floor->fill($validated);

        if ($this->floor_plan_image) {
            $floor->floor_plan_image = ImageUploads::storeAsWebp($this->floor_plan_image, 'floors/plans');
        }

        $floor->save();

        session()->flash('status', 'Kat je spremljen.');

        $this->redirect(route($this->panelPrefix().'buildings.show', $this->building), navigate: true);
    }

    public function render()
    {
        return view('livewire.shared.floors.form', [
            'routePrefix' => $this->panelPrefix(),
        ])->layout($this->panelLayout());
    }
}
