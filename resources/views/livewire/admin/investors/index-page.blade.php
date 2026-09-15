<div>
    <div class="flex items-center justify-between mb-6">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">{{ __('Investitori') }}</h2>

        <a href="{{ route('admin.investors.create') }}" wire:navigate>
            <x-primary-button>{{ __('Novi investitor') }}</x-primary-button>
        </a>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-md bg-green-50 dark:bg-green-900/40 px-4 py-3 text-sm text-green-700 dark:text-green-300">
            {{ session('status') }}
        </div>
    @endif

    <div class="mb-4">
        <x-text-input wire:model.live.debounce.300ms="search" type="search" class="w-full max-w-sm" placeholder="{{ __('Pretraži po nazivu tvrtke...') }}" />
    </div>

    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900/40">
                <tr>
                    <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Tvrtka') }}</th>
                    <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Kontakt nalog') }}</th>
                    <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('Projekti') }}</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($investors as $investor)
                    <tr wire:key="investor-{{ $investor->id }}">
                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                            <a href="{{ route('admin.investors.show', $investor) }}" wire:navigate class="font-medium hover:underline">
                                {{ $investor->company_name }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $investor->user->name }} &lt;{{ $investor->user->email }}&gt;
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $investor->projects_count }}
                        </td>
                        <td class="px-6 py-4 text-end text-sm space-x-3 whitespace-nowrap">
                            <a href="{{ route('admin.investors.edit', $investor) }}" wire:navigate class="text-indigo-600 dark:text-indigo-400 hover:underline">{{ __('Uredi') }}</a>
                            <button type="button" wire:click="delete({{ $investor->id }})" wire:confirm="{{ __('Sigurno želiš obrisati ovog investitora i sve njegove projekte?') }}" class="text-red-600 dark:text-red-400 hover:underline">
                                {{ __('Obriši') }}
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                            {{ __('Nema investitora.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $investors->links() }}
    </div>
</div>
