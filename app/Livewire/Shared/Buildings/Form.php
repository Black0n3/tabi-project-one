<?php

namespace App\Livewire\Shared\Buildings;

use App\Enums\BuildingType;
use App\Livewire\Concerns\ResolvesPanelContext;
use App\Models\Building;
use App\Models\Project;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('Objekat')]
class Form extends Component
{
    use ResolvesPanelContext, WithFileUploads;

    public ?Building $building = null;

    public ?Project $project = null;

    public string $name = '';

    public string $type = '';

    public string $address = '';

    public $facade_image = null;

    public function mount(?Building $building = null, ?int $projectId = null): void
    {
        $this->building = $building;
        $this->type = BuildingType::Zgrada->value;

        if ($building) {
            $this->authorize('update', $building);

            $this->project = $building->project;
            $this->name = $building->name;
            $this->type = $building->type->value;
            $this->address = (string) $building->address;
        } else {
            $this->project = Project::findOrFail($projectId ?? request()->integer('project'));
            $this->authorize('update', $this->project);
        }
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:'.implode(',', array_column(BuildingType::cases(), 'value'))],
            'address' => ['nullable', 'string', 'max:255'],
            'facade_image' => ['nullable', 'image', 'max:4096'],
        ];
    }

    public function save(): void
    {
        $validated = $this->validate();

        $building = $this->building ?? new Building(['project_id' => $this->project->id]);
        $building->fill($validated);

        if ($this->facade_image) {
            $building->facade_image = $this->facade_image->store('buildings/facades', 'public');
        }

        $building->save();

        session()->flash('status', 'Objekat je spremljen.');

        $this->redirect(route($this->panelPrefix().'buildings.show', $building), navigate: true);
    }

    public function render()
    {
        return view('livewire.shared.buildings.form', [
            'routePrefix' => $this->panelPrefix(),
        ])->layout($this->panelLayout());
    }
}
