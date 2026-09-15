<?php

namespace App\Livewire\Admin\Investors;

use App\Models\Investor;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Investitori')]
class IndexPage extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function delete(Investor $investor): void
    {
        $investor->user->delete();

        session()->flash('status', 'Investitor "'.$investor->company_name.'" je obrisan, zajedno sa svim njegovim projektima.');
    }

    public function render()
    {
        $investors = Investor::query()
            ->with('user')
            ->withCount('projects')
            ->when($this->search, fn ($query) => $query->where('company_name', 'like', "%{$this->search}%"))
            ->latest()
            ->paginate(10);

        return view('livewire.admin.investors.index-page', [
            'investors' => $investors,
        ]);
    }
}
