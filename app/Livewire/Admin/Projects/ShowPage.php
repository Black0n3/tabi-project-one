<?php

namespace App\Livewire\Admin\Projects;

use App\Models\Building;
use App\Models\Project;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Projekt')]
class ShowPage extends Component
{
    public Project $project;

    public function mount(Project $project): void
    {
        $this->project = $project;
    }

    public function deleteBuilding(Building $building): void
    {
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

        return view('livewire.admin.projects.show-page', [
            'buildings' => $buildings,
        ]);
    }
}
