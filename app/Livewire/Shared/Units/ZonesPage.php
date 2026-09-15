<?php

namespace App\Livewire\Shared\Units;

use App\Livewire\Concerns\ResolvesPanelContext;
use App\Models\Room;
use App\Models\Unit;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Zone prostorija')]
class ZonesPage extends Component
{
    use ResolvesPanelContext;

    public Unit $unit;

    public function mount(Unit $unit): void
    {
        $this->authorize('update', $unit);

        $this->unit = $unit;
    }

    /**
     * @param  array<int, array{0: float, 1: float}>  $points
     * @return array{id: int, label: string, points: array}
     */
    public function saveZone(?int $roomId, ?string $newLabel, array $points): array
    {
        $this->validatePolygon($points);

        if ($roomId) {
            $room = Room::findOrFail($roomId);
            $this->authorize('update', $room);
            abort_unless($room->unit_id === $this->unit->id, 403);
        } else {
            abort_if(blank($newLabel), 422, 'Naziv prostorije je obavezan.');

            $room = $this->unit->rooms()->create([
                'name' => $newLabel,
            ]);
        }

        $room->update(['polygon' => $points]);

        session()->flash('status', 'Zona prostorije "'.$room->name.'" je spremljena.');

        return ['id' => $room->id, 'label' => $room->name, 'points' => $room->polygon];
    }

    public function deleteZone(?int $roomId): void
    {
        abort_if($roomId === null, 404);

        $room = Room::findOrFail($roomId);
        $this->authorize('update', $room);
        abort_unless($room->unit_id === $this->unit->id, 403);

        $room->update(['polygon' => null]);

        session()->flash('status', 'Zona prostorije "'.$room->name.'" je uklonjena.');
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
        $this->unit->load('building.project.investor');

        $rooms = $this->unit->rooms()->get();

        return view('livewire.shared.units.zones-page', [
            'zones' => $rooms->map(fn (Room $room) => [
                'id' => $room->id,
                'label' => $room->name,
                'points' => $room->polygon,
            ]),
            'planUrl' => $this->unit->floor_plan_image ? Storage::disk('public')->url($this->unit->floor_plan_image) : null,
            'routePrefix' => $this->panelPrefix(),
            'investorUrl' => $this->investorHomeUrl($this->unit->building->project->investor),
        ])->layout($this->panelLayout());
    }
}
