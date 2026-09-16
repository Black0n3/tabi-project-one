<div>
    <div class="flex items-start justify-between mb-6">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">{{ $investor->company_name }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Dobrodošao/la,') }} {{ auth()->user()->name }}.</p>
        </div>

        <a href="{{ route('investitor.projects.create') }}" wire:navigate>
            <x-primary-button>{{ __('Novi projekt') }}</x-primary-button>
        </a>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-md bg-green-50 dark:bg-green-900/40 px-4 py-3 text-sm text-green-700 dark:text-green-300">
            {{ session('status') }}
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900/40">
                <tr>
                    <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Projekt') }}</th>
                    <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Lokacija') }}</th>
                    <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Status') }}</th>
                    <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Objekti') }}</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($projects as $project)
                    <tr wire:key="project-{{ $project->id }}">
                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                            <a href="{{ route('investitor.projects.show', $project) }}" wire:navigate class="font-medium hover:underline">
                                {{ $project->name }}
                            </a>
                            @if ($project->is_featured)
                                <span class="ms-2 inline-flex items-center rounded-full bg-amber-100 dark:bg-amber-900/40 px-2 py-0.5 text-xs text-amber-800 dark:text-amber-300">{{ __('Istaknuto') }}</span>
                            @endif
                            @if ($project->is_hidden)
                                <span class="ms-2 inline-flex items-center rounded-full bg-gray-200 dark:bg-gray-700 px-2 py-0.5 text-xs text-gray-700 dark:text-gray-300">{{ __('Skriveno') }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $project->location }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $project->status->label() }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $project->buildings_count }}</td>
                        <td class="px-6 py-4 text-end text-sm space-x-3 whitespace-nowrap">
                            <a href="{{ route('investitor.projects.edit', $project) }}" wire:navigate class="text-indigo-600 dark:text-indigo-400 hover:underline">{{ __('Uredi') }}</a>
                            <button type="button" wire:click="deleteProject({{ $project->id }})" wire:confirm="{{ __('Sigurno želiš obrisati ovaj projekt?') }}" class="text-red-600 dark:text-red-400 hover:underline">
                                {{ __('Obriši') }}
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                            {{ __('Još nemaš dodanih projekata.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
