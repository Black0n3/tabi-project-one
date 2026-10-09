<div>
    <section class="relative overflow-hidden bg-navy-950">
        <x-hero-backdrop compact />
        <div class="relative max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-28 sm:pt-14 text-white">
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-brand-sky">{{ __('Pretraga') }}</p>
            <h1 class="mt-2 text-4xl sm:text-5xl font-extrabold tracking-tight">{{ __('Sve jedinice') }}</h1>
            <p class="mt-3 max-w-lg text-[15.5px] text-white/75">{{ __('Filtrirajte po lokaciji, sobnosti, kvadraturi i cijeni.') }}</p>
        </div>
    </section>

    @php
        $field = 'w-full rounded-md border-line-strong px-3.5 py-2.5 text-sm text-ink placeholder:text-ink-faint focus:border-brand focus:ring-brand';
    @endphp

    <div class="relative z-10 -mt-16 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="rounded-xl border border-line bg-white p-4 shadow-lift sm:p-5">
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                <input wire:model.live.debounce.300ms="location" type="search" class="{{ $field }}" placeholder="{{ __('Lokacija') }}" aria-label="{{ __('Lokacija') }}">

                <select wire:model.live="projectId" class="{{ $field }}" aria-label="{{ __('Projekt') }}">
                    <option value="">{{ __('Svi projekti') }}</option>
                    @foreach ($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                    @endforeach
                </select>

                <select wire:model.live="status" class="{{ $field }}" aria-label="{{ __('Status') }}">
                    <option value="">{{ __('Svi statusi') }}</option>
                    @foreach ($statuses as $option)
                        <option value="{{ $option->value }}">{{ $option->label() }}</option>
                    @endforeach
                </select>

                <select wire:model.live="type" class="{{ $field }}" aria-label="{{ __('Tip') }}">
                    <option value="">{{ __('Svi tipovi') }}</option>
                    @foreach ($types as $option)
                        <option value="{{ $option->value }}">{{ $option->label() }}</option>
                    @endforeach
                </select>

                <select wire:model.live="roomCount" class="{{ $field }}" aria-label="{{ __('Sobnost') }}">
                    <option value="">{{ __('Sobnost') }}</option>
                    <option value="1">{{ __('1-sobni') }}</option>
                    <option value="2">{{ __('2-sobni') }}</option>
                    <option value="3">{{ __('3-sobni') }}</option>
                    <option value="4+">{{ __('4+ sobni') }}</option>
                </select>

                <input wire:model.live.debounce.300ms="minArea" type="number" min="0" class="{{ $field }}" placeholder="{{ __('Min m²') }}" aria-label="{{ __('Min m²') }}">
                <input wire:model.live.debounce.300ms="maxArea" type="number" min="0" class="{{ $field }}" placeholder="{{ __('Max m²') }}" aria-label="{{ __('Max m²') }}">

                <div class="flex gap-2 col-span-2 sm:col-span-1">
                    <input wire:model.live.debounce.300ms="minPrice" type="number" min="0" class="{{ $field }}" placeholder="{{ __('Min €') }}" aria-label="{{ __('Min €') }}">
                    <input wire:model.live.debounce.300ms="maxPrice" type="number" min="0" class="{{ $field }}" placeholder="{{ __('Max €') }}" aria-label="{{ __('Max €') }}">
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-10">
        @if ($units->isEmpty())
            <p class="text-sm text-ink-soft">{{ __('Nema jedinica koje odgovaraju pretrazi.') }}</p>
        @else
            <p class="mb-6 text-sm font-semibold text-ink-soft">{{ __('Prikazano :from–:to od :total jedinica', ['from' => $units->firstItem(), 'to' => $units->lastItem(), 'total' => $units->total()]) }}</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach ($units as $unit)
                    <x-unit-card :unit="$unit" />
                @endforeach
            </div>

            <div class="mt-12">
                {{ $units->links('vendor.pagination.tabi') }}
            </div>
        @endif
    </div>
</div>
