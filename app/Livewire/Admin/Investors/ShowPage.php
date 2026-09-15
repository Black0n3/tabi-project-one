<?php

namespace App\Livewire\Admin\Investors;

use App\Models\Investor;
use App\Models\Project;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Investitor')]
class ShowPage extends Component
{
    public Investor $investor;

    public function mount(Investor $investor): void
    {
        $this->investor = $investor;
    }

    public function deleteProject(Project $project): void
    {
        $project->delete();

        session()->flash('status', 'Projekt "'.$project->name.'" je obrisan.');
    }

    public function render()
    {
        $this->investor->load('user');

        $projects = $this->investor->projects()
            ->withCount('buildings')
            ->latest()
            ->get();

        return view('livewire.admin.investors.show-page', [
            'projects' => $projects,
        ]);
    }
}
