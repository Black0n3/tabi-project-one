<?php

namespace App\Livewire\Shared\Buildings;

use App\Livewire\Concerns\ResolvesPanelContext;
use App\Models\Building;
use App\Models\Floor;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Zone katova')]
class ZonesPage extends Component
{
    use ResolvesPanelContext;

    public Building $building;

    public function mount(Building $building): void
    {
        $this->authorize('update', $building);

        $this->building = $building;
    }

    /**
     * @param  array<int, array{0: float, 1: float}>  $points
     * @return array{id: int, label: string, points: array}
     */
    public function saveZone(?int $floorId, ?string $newLabel, array $points): array
    {
        $this->validatePolygon($points);

        if ($floorId) {
            $floor = Floor::findOrFail($floorId);
            $this->authorize('update', $floor);
            abort_unless($floor->building_id === $this->building->id, 403);
        } else {
            abort_if(blank($newLabel), 422, 'Naziv kata je obavezan.');

            $floor = $this->building->floors()->create([
                'label' => $newLabel,
                'order' => $this->building->floors()->count(),
            ]);
        }

        $floor->update(['polygon' => $points]);

        session()->flash('status', 'Zona kata "'.$floor->label.'" je spremljena.');

        return ['id' => $floor->id, 'label' => $floor->label, 'points' => $floor->polygon];
    }

    public function deleteZone(?int $floorId): void
    {
        abort_if($floorId === null, 404);

        $floor = Floor::findOrFail($floorId);
        $this->authorize('update', $floor);
        abort_unless($floor->building_id === $this->building->id, 403);

        $floor->update(['polygon' => null]);

        session()->flash('status', 'Zona kata "'.$floor->label.'" je uklonjena.');
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
        $this->building->load('project.investor');

        $floors = $this->building->floors()->get();

        return view('livewire.shared.buildings.zones-page', [
            'zones' => $floors->map(fn (Floor $floor) => [
                'id' => $floor->id,
                'label' => $floor->label,
                'points' => $floor->polygon,
            ]),
            'facadeUrl' => $this->building->facade_image ? Storage::disk('public')->url($this->building->facade_image) : null,
            'routePrefix' => $this->panelPrefix(),
            'investorUrl' => $this->investorHomeUrl($this->building->project->investor),
        ])->layout($this->panelLayout());
    }
}
