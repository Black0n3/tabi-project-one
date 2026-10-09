<div>
    <div class="flex items-center justify-between mb-6">
        <h2 class="font-extrabold text-2xl tracking-tight text-navy-900">{{ __('Investitori') }}</h2>

        <a href="{{ route('admin.investors.create') }}" wire:navigate>
            <x-primary-button>{{ __('Novi investitor') }}</x-primary-button>
        </a>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-md bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('status') }}
        </div>
    @endif

    <div class="mb-4">
        <x-text-input wire:model.live.debounce.300ms="search" type="search" class="w-full max-w-sm" placeholder="{{ __('Pretraži po nazivu tvrtke...') }}" />
    </div>

    <div class="bg-white shadow-card rounded-xl border border-line overflow-hidden">
        <table class="min-w-full divide-y divide-line">
            <thead class="bg-mist">
                <tr>
                    <th class="px-6 py-3 text-start text-xs font-bold text-ink-soft uppercase tracking-wider">{{ __('Tvrtka') }}</th>
                    <th class="px-6 py-3 text-start text-xs font-bold text-ink-soft uppercase tracking-wider">{{ __('Kontakt nalog') }}</th>
                    <th class="px-6 py-3 text-start text-xs font-bold text-ink-soft uppercase tracking-wider">{{ __('Projekti') }}</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-line">
                @forelse ($investors as $investor)
                    <tr wire:key="investor-{{ $investor->id }}">
                        <td class="px-6 py-4 text-sm text-navy-900">
                            <a href="{{ route('admin.investors.show', $investor) }}" wire:navigate class="font-medium hover:underline">
                                {{ $investor->company_name }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-ink-soft">
                            {{ $investor->user->name }} &lt;{{ $investor->user->email }}&gt;
                        </td>
                        <td class="px-6 py-4 text-sm text-ink-soft">
                            {{ $investor->projects_count }}
                        </td>
                        <td class="px-6 py-4 text-end text-sm space-x-3 whitespace-nowrap">
                            <a href="{{ route('admin.investors.edit', $investor) }}" wire:navigate class="text-brand hover:underline">{{ __('Uredi') }}</a>
                            <button type="button" wire:click="delete({{ $investor->id }})" wire:confirm="{{ __('Sigurno želiš obrisati ovog investitora i sve njegove projekte?') }}" class="text-red-600 hover:underline">
                                {{ __('Obriši') }}
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-sm text-ink-soft">
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
