<?php

namespace App\Livewire\Public;

use App\Models\Project;
use App\Models\Unit;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Naslovna')]
class HomePage extends Component
{
    public function render()
    {
        $projects = Project::query()
            ->with('investor')
            ->withCount('buildings')
            ->orderByDesc('is_featured')
            ->latest()
            ->take(6)
            ->get();

        $units = Unit::query()
            ->with('building.project')
            ->orderByDesc('is_featured')
            ->latest()
            ->take(8)
            ->get();

        return view('livewire.public.home-page', [
            'projects' => $projects,
            'units' => $units,
        ])->layout('layouts.public');
    }
}
