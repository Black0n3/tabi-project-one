<?php

namespace App\Livewire\Shared\Floors;

use App\Enums\UnitStatus;
use App\Enums\UnitType;
use App\Livewire\Concerns\ResolvesPanelContext;
use App\Models\Floor;
use App\Models\Unit;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Zone jedinica')]
class ZonesPage extends Component
{
    use ResolvesPanelContext;

    public Floor $floor;

    public function mount(Floor $floor): void
    {
        $this->authorize('update', $floor);

        $this->floor = $floor;
    }

    /**
     * @param  array<int, array{0: float, 1: float}>  $points
     * @return array{id: int, label: string, points: array}
     */
    public function saveZone(?int $unitId, ?string $newLabel, array $points): array
    {
        $this->validatePolygon($points);

        if ($unitId) {
            $unit = Unit::findOrFail($unitId);
            $this->authorize('update', $unit);
            abort_unless($unit->floor_id === $this->floor->id, 403);
        } else {
            abort_if(blank($newLabel), 422, 'Oznaka jedinice je obavezna.');

            $unit = $this->floor->units()->create([
                'building_id' => $this->floor->building_id,
                'code' => $newLabel,
                'type' => UnitType::Stan,
                'area_m2' => 0,
                'status' => UnitStatus::Dostupno,
            ]);
        }

        $unit->update(['polygon' => $points]);

        session()->flash('status', 'Zona jedinice "'.$unit->code.'" je spremljena.');

        return ['id' => $unit->id, 'label' => $unit->code, 'points' => $unit->polygon];
    }

    public function deleteZone(?int $unitId): void
    {
        abort_if($unitId === null, 404);

        $unit = Unit::findOrFail($unitId);
        $this->authorize('update', $unit);
        abort_unless($unit->floor_id === $this->floor->id, 403);

        $unit->update(['polygon' => null]);

        session()->flash('status', 'Zona jedinice "'.$unit->code.'" je uklonjena.');
    }

    /**
     * @param  array<int, mixed>  $points
     */
    protected function validatePolygon(array $points): void
    {
        abort_if(count($points) < 3, 422, 'Poligon mora imati barem 3 točke.');

        foreach ($points as $point) {
            abort_unless(
                is_array($point) && count($point) === 2 && is_numeric($point[0]) && is_numeric($point[1])
                    && $point[0] >= 0 && $point[0] <= 100 && $point[1] >= 0 && $point[1] <= 100,
                422,
                'Neispravne koordinate točke.'
            );
        }
    }

    public function render()
    {
        $this->floor->load('building.project.investor');

        $units = $this->floor->units()->get();

        return view('livewire.shared.floors.zones-page', [
            'zones' => $units->map(fn (Unit $unit) => [
                'id' => $unit->id,
                'label' => $unit->code,
                'points' => $unit->polygon,
            ]),
            'planUrl' => $this->floor->floor_plan_image ? Storage::disk('public')->url($this->floor->floor_plan_image) : null,
            'routePrefix' => $this->panelPrefix(),
            'investorUrl' => $this->investorHomeUrl($this->floor->building->project->investor),
        ])->layout($this->panelLayout());
    }
}
