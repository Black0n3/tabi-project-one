<?php

namespace App\Livewire\Investor;

use App\Models\Investor;
use App\Models\Project;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.investor')]
#[Title('Moji projekti')]
class DashboardPage extends Component
{
    public Investor $investor;

    public function mount(): void
    {
        $this->investor = auth()->user()->investor()->firstOrFail();
    }

    public function deleteProject(Project $project): void
    {
        $this->authorize('delete', $project);

        $project->delete();

        session()->flash('status', 'Projekt "'.$project->name.'" je obrisan.');
    }

    public function render()
    {
        $projects = $this->investor->projects()
            ->withCount('buildings')
            ->latest()
            ->get();

        return view('livewire.investor.dashboard-page', [
            'projects' => $projects,
        ]);
    }
}
