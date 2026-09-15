@props(['status'])

<span @class([
    'inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium shrink-0',
    'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-300' => $status === \App\Enums\UnitStatus::Dostupno,
    'bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-300' => $status === \App\Enums\UnitStatus::Rezervirano,
    'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' => $status === \App\Enums\UnitStatus::Prodano,
])>{{ $status->label() }}</span>
