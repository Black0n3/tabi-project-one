<?php

namespace App\Livewire\Admin\Units;

use App\Enums\UnitStatus;
use App\Enums\UnitType;
use App\Models\Building;
use App\Models\Unit;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.admin')]
#[Title('Jedinica')]
class Form extends Component
{
    use WithFileUploads;

    public ?Unit $unit = null;

    public ?Building $building = null;

    public string $code = '';

    public string $type = '';

    public string $area_m2 = '';

    public string $price = '';

    public string $status = '';

    public ?int $floor_id = null;

    public string $description = '';

    public bool $is_featured = false;

    public $floor_plan_image = null;

    public function mount(?Unit $unit = null, ?int $buildingId = null): void
    {
        $this->unit = $unit;
        $this->type = UnitType::Stan->value;
        $this->status = UnitStatus::Dostupno->value;

        if ($unit) {
            $this->building = $unit->building;
            $this->code = $unit->code;
            $this->type = $unit->type->value;
            $this->area_m2 = (string) $unit->area_m2;
            $this->price = (string) $unit->price;
            $this->status = $unit->status->value;
            $this->floor_id = $unit->floor_id;
            $this->description = (string) $unit->description;
            $this->is_featured = $unit->is_featured;
        } else {
            $this->building = Building::findOrFail($buildingId ?? request()->integer('building'));
        }
    }

    protected function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:'.implode(',', array_column(UnitType::cases(), 'value'))],
            'area_m2' => ['required', 'numeric', 'min:0'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'string', 'in:'.implode(',', array_column(UnitStatus::cases(), 'value'))],
            'floor_id' => ['nullable', 'exists:floors,id'],
            'description' => ['nullable', 'string'],
            'is_featured' => ['boolean'],
            'floor_plan_image' => ['nullable', 'image', 'max:4096'],
        ];
    }

    public function save(): void
    {
        $validated = $this->validate();
        $validated['price'] = $validated['price'] ?: null;

        $unit = $this->unit ?? new Unit(['building_id' => $this->building->id]);
        $unit->fill($validated);

        if ($this->floor_plan_image) {
            $unit->floor_plan_image = $this->floor_plan_image->store('units/plans', 'public');
        }

        $unit->save();

        session()->flash('status', 'Jedinica je spremljena.');

        $this->redirect(route('admin.units.show', $unit), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.units.form', [
            'floors' => $this->building->floors,
        ]);
    }
}
