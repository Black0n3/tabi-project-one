<?php

namespace App\Livewire\Public;

use App\Models\Project;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Title;
use Livewire\Component;

class ProjectPage extends Component
{
    public Project $project;

    public function mount(Project $project): void
    {
        $this->project = $project;
    }

    public function render()
    {
        $this->project->load('investor');

        $buildings = $this->project->buildings()
            ->withCount('units')
            ->get();

        return view('livewire.public.project-page', [
            'buildings' => $buildings,
        ])
            ->layout('layouts.public', [
                'title' => $this->project->name,
                'description' => $this->project->description ?: "{$this->project->name} — {$this->project->location}",
                'image' => $this->project->cover_image ? Storage::disk('public')->url($this->project->cover_image) : null,
            ]);
    }
}
