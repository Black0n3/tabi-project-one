<?php

namespace App\Livewire\Public;

use App\Enums\UnitStatus;
use App\Enums\UnitType;
use App\Models\Unit;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Sve jedinice')]
class UnitsIndex extends Component
{
    use WithPagination;

    #[Url(as: 'lokacija')]
    public string $location = '';

    #[Url(as: 'status')]
    public string $status = '';

    #[Url(as: 'tip')]
    public string $type = '';

    #[Url(as: 'min_m2')]
    public string $minArea = '';

    #[Url(as: 'max_m2')]
    public string $maxArea = '';

    #[Url(as: 'min_cijena')]
    public string $minPrice = '';

    #[Url(as: 'max_cijena')]
    public string $maxPrice = '';

    public function updating(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $units = Unit::query()
            ->whereHas('building.project', fn ($query) => $query->visible())
            ->with('building.project')
            ->when($this->status, fn ($query) => $query->where('status', $this->status))
            ->when($this->type, fn ($query) => $query->where('type', $this->type))
            ->when($this->minArea !== '', fn ($query) => $query->where('area_m2', '>=', (float) $this->minArea))
            ->when($this->maxArea !== '', fn ($query) => $query->where('area_m2', '<=', (float) $this->maxArea))
            ->when($this->minPrice !== '', fn ($query) => $query->where('price', '>=', (float) $this->minPrice))
            ->when($this->maxPrice !== '', fn ($query) => $query->where('price', '<=', (float) $this->maxPrice))
            ->when($this->location, function ($query) {
                $query->whereHas('building.project', fn ($q) => $q->where('location', 'like', "%{$this->location}%"));
            })
            ->orderByDesc('is_featured')
            ->latest()
            ->paginate(12);

        return view('livewire.public.units-index', [
            'units' => $units,
            'statuses' => UnitStatus::cases(),
            'types' => UnitType::cases(),
        ])->layout('layouts.public', [
            'description' => 'Pregledaj sve dostupne stanove i kuće naših investitora.',
        ]);
    }
}
