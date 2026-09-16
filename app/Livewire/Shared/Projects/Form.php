<?php

namespace App\Livewire\Shared\Projects;

use App\Enums\ProjectStatus;
use App\Livewire\Concerns\ResolvesPanelContext;
use App\Models\Investor;
use App\Models\Project;
use App\Support\ImageUploads;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('Projekt')]
class Form extends Component
{
    use ResolvesPanelContext, WithFileUploads;

    public ?Project $project = null;

    public ?Investor $investor = null;

    public string $name = '';

    public string $description = '';

    public string $location = '';

    public string $status = '';

    public bool $is_featured = false;

    public bool $is_hidden = false;

    public $cover_image = null;

    public function mount(?Project $project = null, ?int $investorId = null): void
    {
        $this->project = $project;
        $this->status = ProjectStatus::InProgress->value;

        if ($project) {
            $this->authorize('update', $project);

            $this->investor = $project->investor;
            $this->name = $project->name;
            $this->description = (string) $project->description;
            $this->location = (string) $project->location;
            $this->status = $project->status->value;
            $this->is_featured = $project->is_featured;
            $this->is_hidden = $project->is_hidden;
        } elseif (auth()->user()->isAdmin()) {
            $this->investor = Investor::findOrFail($investorId ?? request()->integer('investor'));
        } else {
            $this->investor = auth()->user()->investor()->firstOrFail();
        }
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string', 'in:'.implode(',', array_column(ProjectStatus::cases(), 'value'))],
            'is_featured' => ['boolean'],
            'is_hidden' => ['boolean'],
            'cover_image' => ['nullable', 'image', 'max:8192'],
        ];
    }

    public function save(): void
    {
        $validated = $this->validate();

        $project = $this->project ?? new Project(['investor_id' => $this->investor->id]);
        $project->fill($validated);

        if ($this->cover_image) {
            $project->cover_image = ImageUploads::storeAsWebp($this->cover_image, 'projects/covers');
        }

        $project->save();

        session()->flash('status', 'Projekt je spremljen.');

        $this->redirect(route($this->panelPrefix().'projects.show', $project), navigate: true);
    }

    public function render()
    {
        return view('livewire.shared.projects.form', [
            'routePrefix' => $this->panelPrefix(),
            'investorUrl' => $this->investorHomeUrl($this->investor),
        ])->layout($this->panelLayout());
    }
}
