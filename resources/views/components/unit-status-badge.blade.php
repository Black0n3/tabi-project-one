@props(['status'])

<span @class([
    'inline-flex items-center rounded px-2.5 py-1 text-[11px] font-bold uppercase tracking-wide shrink-0',
    'bg-emerald-600 text-white' => $status === \App\Enums\UnitStatus::Dostupno,
    'bg-amber-500 text-navy-950' => $status === \App\Enums\UnitStatus::Rezervirano,
    'bg-slate-500 text-white' => $status === \App\Enums\UnitStatus::Prodano,
])>{{ $status->label() }}</span>
