<div>
    <section class="relative overflow-hidden border-b border-line bg-canvas">
        <x-hero-backdrop compact />
        <div class="relative max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-14 sm:py-16">
            <span class="text-[11px] font-bold uppercase tracking-[0.18em] text-ink-faint">{{ __('Pretraga') }}</span>
            <h1 class="mt-3.5 font-display text-4xl sm:text-[44px] font-medium leading-tight text-ink">{{ __('Svi projekti') }}</h1>
            <p class="mt-3 max-w-lg text-[15.5px] text-ink-soft">{{ __('Pregledajte trenutno aktivne projekte novogradnje u Osijeku i Slavoniji.') }}</p>
        </div>
    </section>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-7 pb-8">
        <div class="rounded-[18px] border border-line bg-panel p-4 flex flex-col sm:flex-row sm:items-center gap-3">
            <input wire:model.live.debounce.300ms="location" type="search" class="w-full sm:flex-1 rounded-xl border-line-strong bg-canvas-raised px-3.5 py-2.5 text-sm text-ink placeholder:text-ink-faint focus:border-white/40 focus:ring-white/20" placeholder="{{ __('Pretraži po lokaciji...') }}" aria-label="{{ __('Lokacija') }}">

            <select wire:model.live="status" aria-label="{{ __('Status') }}" class="rounded-xl border-line-strong bg-canvas-raised px-3.5 py-2.5 pe-9 text-sm text-ink focus:border-white/40 focus:ring-white/20">
                <option value="">{{ __('Svi statusi') }}</option>
                @foreach ($statuses as $option)
                    <option value="{{ $option->value }}">{{ $option->label() }}</option>
                @endforeach
            </select>

            <span class="sm:ms-auto text-[13px] text-ink-faint">{{ trans_choice('{1}:count projekt|[2,4]:count projekta|[5,*]:count projekata', $projects->total(), ['count' => $projects->total()]) }}</span>
        </div>
    </div>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">
        @if ($projects->isEmpty())
            <p class="text-sm text-ink-soft">{{ __('Nema projekata koji odgovaraju pretrazi.') }}</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-7">
                @foreach ($projects as $project)
                    <x-project-card :project="$project" />
                @endforeach
            </div>

            <div class="mt-12">
                {{ $projects->links('vendor.pagination.tabi') }}
            </div>
        @endif
    </div>
</div>
