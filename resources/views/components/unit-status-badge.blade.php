@props(['status'])

<span @class([
    'inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-bold shrink-0',
    'bg-ink text-canvas' => $status === \App\Enums\UnitStatus::Dostupno,
    'border border-white/50 text-ink' => $status === \App\Enums\UnitStatus::Rezervirano,
    'bg-white/[0.06] border border-line text-ink-faint' => $status === \App\Enums\UnitStatus::Prodano,
])>{{ $status->label() }}</span>
