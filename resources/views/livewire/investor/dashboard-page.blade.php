<div>
    <div class="flex items-start justify-between mb-6">
        <div>
            <h2 class="font-extrabold text-2xl tracking-tight text-navy-900">{{ $investor->company_name }}</h2>
            <p class="text-sm text-ink-soft">{{ __('Dobrodošao/la,') }} {{ auth()->user()->name }}.</p>
        </div>

        <a href="{{ route('investitor.projects.create') }}" wire:navigate>
            <x-primary-button>{{ __('Novi projekt') }}</x-primary-button>
        </a>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-md bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('status') }}
        </div>
    @endif

    <div class="bg-white shadow-card rounded-xl border border-line overflow-hidden">
        <table class="min-w-full divide-y divide-line">
            <thead class="bg-mist">
                <tr>
                    <th class="px-6 py-3 text-start text-xs font-bold text-ink-soft uppercase tracking-wider">{{ __('Projekt') }}</th>
                    <th class="px-6 py-3 text-start text-xs font-bold text-ink-soft uppercase tracking-wider">{{ __('Lokacija') }}</th>
                    <th class="px-6 py-3 text-start text-xs font-bold text-ink-soft uppercase tracking-wider">{{ __('Status') }}</th>
                    <th class="px-6 py-3 text-start text-xs font-bold text-ink-soft uppercase tracking-wider">{{ __('Objekti') }}</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-line">
                @forelse ($projects as $project)
                    <tr wire:key="project-{{ $project->id }}">
                        <td class="px-6 py-4 text-sm text-navy-900">
                            <a href="{{ route('investitor.projects.show', $project) }}" wire:navigate class="font-medium hover:underline">
                                {{ $project->name }}
                            </a>
                            @if ($project->is_featured)
                                <span class="ms-2 inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-xs text-amber-800">{{ __('Istaknuto') }}</span>
                            @endif
                            @if ($project->is_hidden)
                                <span class="ms-2 inline-flex items-center rounded-full bg-line px-2 py-0.5 text-xs text-ink">{{ __('Skriveno') }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-ink-soft">{{ $project->location }}</td>
                        <td class="px-6 py-4 text-sm text-ink-soft">{{ $project->status->label() }}</td>
                        <td class="px-6 py-4 text-sm text-ink-soft">{{ $project->buildings_count }}</td>
                        <td class="px-6 py-4 text-end text-sm space-x-3 whitespace-nowrap">
                            <a href="{{ route('investitor.projects.edit', $project) }}" wire:navigate class="text-brand hover:underline">{{ __('Uredi') }}</a>
                            <button type="button" wire:click="deleteProject({{ $project->id }})" wire:confirm="{{ __('Sigurno želiš obrisati ovaj projekt?') }}" class="text-red-600 hover:underline">
                                {{ __('Obriši') }}
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-sm text-ink-soft">
                            {{ __('Još nemaš dodanih projekata.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
