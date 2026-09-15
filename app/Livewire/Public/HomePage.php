<?php

namespace App\Livewire\Public;

use App\Enums\UnitStatus;
use App\Models\Project;
use App\Models\Unit;
use Illuminate\Support\Facades\Storage;
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

        $heroImage = Project::query()
            ->whereNotNull('cover_image')
            ->orderByDesc('is_featured')
            ->latest()
            ->value('cover_image');

        return view('livewire.public.home-page', [
            'projects' => $projects,
            'units' => $units,
            'heroImage' => $heroImage ? Storage::disk('public')->url($heroImage) : null,
            'stats' => [
                'projects' => Project::count(),
                'units' => Unit::count(),
                'available' => Unit::where('status', UnitStatus::Dostupno)->count(),
                'locations' => Project::query()->whereNotNull('location')->distinct('location')->count('location'),
            ],
        ])->layout('layouts.public');
    }
}
