<div>
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">
            <a href="{{ route('public.projects.show', $building->project) }}" wire:navigate class="hover:underline">{{ $building->project->name }}</a>
        </p>
        <h1 class="text-2xl sm:text-3xl font-bold">{{ $building->name }}</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">
            {{ $building->type->label() }}
            @if ($building->address) &middot; {{ $building->address }} @endif
        </p>
    </div>

    <div
        class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-16"
        x-data="buildingViewer({ floors: @js($floorsData) })"
        x-init="init()"
    >
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
            <div class="relative inline-block border border-gray-200 dark:border-gray-800 rounded-lg overflow-hidden bg-gray-50 dark:bg-gray-900 w-full">
                @if ($facadeUrl)
                    <img x-ref="image" src="{{ $facadeUrl }}" @load="updateRect" class="block w-full h-auto select-none" draggable="false" alt="{{ $building->name }}">
                    <svg
                        x-ref="svg"
                        x-html="renderSvg()"
                        class="absolute inset-0 w-full h-full"
                        @mousemove="onMove($event)"
                        @click="onClick($event)"
                        style="cursor: pointer;"
                    ></svg>
                @else
                    <div class="aspect-[4/3] flex items-center justify-center text-gray-400 dark:text-gray-600 text-sm p-8 text-center">
                        {{ __('Fasada objekta još nije dodana.') }}
                    </div>
                @endif
            </div>

            <div>
                <template x-if="floors.length > 1">
                    <div class="flex flex-wrap gap-2 mb-6">
                        <template x-for="floor in floors" :key="floor.id">
                            <button
                                type="button"
                                @mouseenter="select(floor.id)"
                                @click="select(floor.id)"
                                :class="floor.id === activeId ? 'bg-gray-900 dark:bg-gray-100 text-white dark:text-gray-900' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700'"
                                class="px-3 py-1.5 rounded-md text-sm font-medium transition"
                                x-text="floor.label"
                            ></button>
                        </template>
                    </div>
                </template>

                <template x-if="active()">
                    <div>
                        <h2 class="font-semibold text-lg mb-3" x-text="active().label"></h2>

                        <div class="space-y-2" x-show="active().units.length > 0">
                            <template x-for="unit in active().units" :key="unit.id">
                                <a :href="unit.url" wire:navigate class="flex items-center justify-between gap-3 rounded-md border border-gray-200 dark:border-gray-800 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                                    <div>
                                        <span class="font-medium" x-text="unit.code"></span>
                                        <span class="text-sm text-gray-500 dark:text-gray-400" x-text="unit.area + ' m²'"></span>
                                    </div>
                                    <span
                                        class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium shrink-0"
                                        :class="{
                                            'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-300': unit.status === 'dostupno',
                                            'bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-300': unit.status === 'rezervirano',
                                            'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300': unit.status === 'prodano',
                                        }"
                                        x-text="unit.statusLabel"
                                    ></span>
                                </a>
                            </template>
                        </div>

                        <p class="text-sm text-gray-500 dark:text-gray-400" x-show="active().units.length === 0">
                            {{ __('Na ovom katu još nema unesenih jedinica.') }}
                        </p>
                    </div>
                </template>

                <template x-if="!active()">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ __('Prijeđi mišem preko kata na slici (ili ga dodirni) da vidiš dostupne jedinice.') }}
                    </p>
                </template>
            </div>
        </div>
    </div>

    @if ($unassignedUnits->isNotEmpty())
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
            <h2 class="text-lg font-semibold mb-6">{{ __('Jedinice') }}</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($unassignedUnits as $unit)
                    <a href="{{ route('public.units.show', $unit) }}" wire:navigate class="flex items-center justify-between gap-3 rounded-md border border-gray-200 dark:border-gray-800 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                        <div>
                            <span class="font-medium">{{ $unit->code }}</span>
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $unit->area_m2 }} m²</span>
                        </div>
                        <x-unit-status-badge :status="$unit->status" />
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>

@once
    @push('scripts')
        <script>
            function buildingViewer({ floors }) {
                return {
                    floors: floors.map(f => ({ ...f, points: f.points || [] })),
                    activeId: null,
                    imgW: 0,
                    imgH: 0,

                    init() {
                        this.updateRect();
                        window.addEventListener('resize', () => this.updateRect());

                        const withShape = this.floors.find(f => this.hasShape(f.points));
                        this.activeId = withShape ? withShape.id : (this.floors[0]?.id ?? null);
                    },

                    updateRect() {
                        if (!this.$refs.image) return;
                        this.imgW = this.$refs.image.clientWidth;
                        this.imgH = this.$refs.image.clientHeight;
                    },

                    hasShape(points) {
                        return Array.isArray(points) && points.length >= 3;
                    },

                    toPx(point) {
                        return [(point[0] / 100) * this.imgW, (point[1] / 100) * this.imgH];
                    },

                    toSvgPoints(points) {
                        return points.map(p => this.toPx(p).join(',')).join(' ');
                    },

                    select(id) {
                        this.activeId = id;
                    },

                    active() {
                        return this.floors.find(f => f.id === this.activeId) ?? null;
                    },

                    pointInPolygon(x, y, points) {
                        let inside = false;
                        for (let i = 0, j = points.length - 1; i < points.length; j = i++) {
                            const [xi, yi] = this.toPx(points[i]);
                            const [xj, yj] = this.toPx(points[j]);
                            const intersect = ((yi > y) !== (yj > y)) && (x < (xj - xi) * (y - yi) / (yj - yi) + xi);
                            if (intersect) inside = !inside;
                        }
                        return inside;
                    },

                    floorAt(e) {
                        const rect = this.$refs.svg.getBoundingClientRect();
                        const x = e.clientX - rect.left;
                        const y = e.clientY - rect.top;
                        return this.floors.find(f => this.hasShape(f.points) && this.pointInPolygon(x, y, f.points));
                    },

                    onMove(e) {
                        const floor = this.floorAt(e);
                        if (floor) this.activeId = floor.id;
                    },

                    onClick(e) {
                        const floor = this.floorAt(e);
                        if (floor) this.activeId = floor.id;
                    },

                    renderSvg() {
                        let html = '';
                        for (const floor of this.floors) {
                            if (!this.hasShape(floor.points)) continue;
                            const active = floor.id === this.activeId;
                            html += `<polygon points="${this.toSvgPoints(floor.points)}" `
                                + `class="${active ? 'fill-indigo-500/35 stroke-indigo-600' : 'fill-white/10 stroke-white/70 hover:fill-indigo-500/20'}" `
                                + `stroke-width="2"></polygon>`;
                        }
                        return html;
                    },
                };
            }
        </script>
    @endpush
@endonce
