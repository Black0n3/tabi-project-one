<?php

namespace App\Livewire\Public;

use App\Enums\ProjectStatus;
use App\Models\Project;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Svi projekti')]
class ProjectsIndex extends Component
{
    use WithPagination;

    #[Url(as: 'lokacija')]
    public string $location = '';

    #[Url(as: 'status')]
    public string $status = '';

    public function updatingLocation(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $projects = Project::query()
            ->visible()
            ->with('investor')
            ->withCount('buildings')
            ->when($this->location, fn ($query) => $query->where('location', 'like', "%{$this->location}%"))
            ->when($this->status, fn ($query) => $query->where('status', $this->status))
            ->orderByDesc('is_featured')
            ->latest()
            ->paginate(9);

        return view('livewire.public.projects-index', [
            'projects' => $projects,
            'statuses' => ProjectStatus::cases(),
        ])->layout('layouts.public', [
            'description' => 'Pregledaj sve stambene projekte naših investitora.',
        ]);
    }
}
