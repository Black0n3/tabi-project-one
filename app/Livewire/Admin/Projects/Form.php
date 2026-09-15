<?php

namespace App\Livewire\Admin\Projects;

use App\Enums\ProjectStatus;
use App\Models\Investor;
use App\Models\Project;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.admin')]
#[Title('Projekt')]
class Form extends Component
{
    use WithFileUploads;

    public ?Project $project = null;

    public ?Investor $investor = null;

    public string $name = '';

    public string $description = '';

    public string $location = '';

    public string $status = '';

    public bool $is_featured = false;

    public $cover_image = null;

    public function mount(?Project $project = null, ?int $investorId = null): void
    {
        $this->project = $project;
        $this->status = ProjectStatus::InProgress->value;

        if ($project) {
            $this->investor = $project->investor;
            $this->name = $project->name;
            $this->description = (string) $project->description;
            $this->location = (string) $project->location;
            $this->status = $project->status->value;
            $this->is_featured = $project->is_featured;
        } else {
            $this->investor = Investor::findOrFail($investorId ?? request()->integer('investor'));
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
            'cover_image' => ['nullable', 'image', 'max:4096'],
        ];
    }

    public function save(): void
    {
        $validated = $this->validate();

        $project = $this->project ?? new Project(['investor_id' => $this->investor->id]);
        $project->fill($validated);

        if ($this->cover_image) {
            $project->cover_image = $this->cover_image->store('projects/covers', 'public');
        }

        $project->save();

        session()->flash('status', 'Projekt je spremljen.');

        $this->redirect(route('admin.projects.show', $project), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.projects.form');
    }
}
