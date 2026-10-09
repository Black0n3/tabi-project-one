<div>
    <section class="relative overflow-hidden bg-navy-950">
        <x-hero-backdrop compact />
        <div class="relative max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-24 sm:pt-14 text-white">
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-brand-sky">{{ __('Pretraga') }}</p>
            <h1 class="mt-2 text-4xl sm:text-5xl font-extrabold tracking-tight">{{ __('Svi projekti') }}</h1>
            <p class="mt-3 max-w-lg text-[15.5px] text-white/75">{{ __('Pregledajte trenutno aktivne projekte novogradnje u Osijeku i Slavoniji.') }}</p>
        </div>
    </section>

    <div class="relative z-10 -mt-12 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-3 rounded-xl border border-line bg-white p-3 shadow-lift sm:flex-row sm:items-center sm:p-4">
            <input wire:model.live.debounce.300ms="location" type="search" class="w-full rounded-md border-line-strong px-4 py-3 text-sm text-ink placeholder:text-ink-faint focus:border-brand focus:ring-brand sm:flex-1" placeholder="{{ __('Pretraži po lokaciji...') }}" aria-label="{{ __('Lokacija') }}">

            <select wire:model.live="status" aria-label="{{ __('Status') }}" class="rounded-md border-line-strong px-4 py-3 pe-10 text-sm text-ink focus:border-brand focus:ring-brand">
                <option value="">{{ __('Svi statusi') }}</option>
                @foreach ($statuses as $option)
                    <option value="{{ $option->value }}">{{ $option->label() }}</option>
                @endforeach
            </select>

            <span class="px-2 text-sm font-semibold text-ink-soft sm:ms-auto">{{ trans_choice('{1}:count projekt|[2,4]:count projekta|[5,*]:count projekata', $projects->total(), ['count' => $projects->total()]) }}</span>
        </div>
    </div>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-10">
        @if ($projects->isEmpty())
            <p class="text-sm text-ink-soft">{{ __('Nema projekata koji odgovaraju pretrazi.') }}</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
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
