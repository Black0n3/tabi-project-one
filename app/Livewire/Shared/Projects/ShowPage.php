<?php

namespace App\Livewire\Shared\Projects;

use App\Livewire\Concerns\ResolvesPanelContext;
use App\Models\Building;
use App\Models\Project;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Projekt')]
class ShowPage extends Component
{
    use ResolvesPanelContext;

    public Project $project;

    public function mount(Project $project): void
    {
        $this->authorize('view', $project);

        $this->project = $project;
    }

    public function deleteBuilding(Building $building): void
    {
        $this->authorize('delete', $building);

        $building->delete();

        session()->flash('status', 'Objekat "'.$building->name.'" je obrisan.');
    }

    public function render()
    {
        $this->project->load('investor');

        $buildings = $this->project->buildings()
            ->withCount(['floors', 'units'])
            ->latest()
            ->get();

        return view('livewire.shared.projects.show-page', [
            'buildings' => $buildings,
            'routePrefix' => $this->panelPrefix(),
            'investorUrl' => $this->investorHomeUrl($this->project->investor),
        ])->layout($this->panelLayout());
    }
}
