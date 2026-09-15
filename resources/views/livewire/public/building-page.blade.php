<div>
    <x-page-hero :image="$facadeUrl">
        <p class="text-sm text-emerald-300/90 mb-3">
            <a href="{{ route('public.projects.show', $building->project) }}" wire:navigate class="hover:underline">{{ $building->project->name }}</a>
        </p>
        <h1 class="font-display text-3xl sm:text-5xl font-semibold leading-tight">{{ $building->name }}</h1>
        <p class="mt-4 flex flex-wrap items-center gap-x-3 gap-y-1 text-emerald-100/80">
            <span class="inline-flex items-center rounded-full bg-white/10 px-3 py-1 text-sm font-medium">{{ $building->type->label() }}</span>
            @if ($building->address)
                <span class="text-emerald-300/50">&middot;</span>
                <span>{{ $building->address }}</span>
            @endif
        </p>
    </x-page-hero>

    <div
        class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-14 sm:py-16"
        x-data="buildingViewer({ floors: @js($floorsData) })"
        x-init="init()"
    >
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
            <div class="relative inline-block rounded-2xl border border-stone-200 dark:border-stone-800 shadow-sm overflow-hidden bg-stone-100 dark:bg-stone-900 w-full">
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
                    <div class="aspect-[4/3] flex flex-col items-center justify-center gap-3 text-stone-400 dark:text-stone-600 text-sm p-8 text-center">
                        <x-building-placeholder-icon class="h-10 w-10" />
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
                                :class="floor.id === activeId ? 'bg-emerald-900 dark:bg-emerald-800 text-white' : 'bg-stone-100 dark:bg-stone-800 text-stone-700 dark:text-stone-300 hover:bg-stone-200 dark:hover:bg-stone-700'"
                                class="px-3.5 py-1.5 rounded-full text-sm font-medium transition"
                                x-text="floor.label"
                            ></button>
                        </template>
                    </div>
                </template>

                <template x-if="active()">
                    <div>
                        <h2 class="font-display font-semibold text-xl mb-4" x-text="active().label"></h2>

                        <div class="space-y-2.5" x-show="active().units.length > 0">
                            <template x-for="unit in active().units" :key="unit.id">
                                <a :href="unit.url" wire:navigate class="flex items-center justify-between gap-3 rounded-xl border border-stone-200 dark:border-stone-800 bg-white dark:bg-stone-900 px-5 py-4 hover:border-emerald-300 dark:hover:border-emerald-700 hover:shadow-md transition">
                                    <div>
                                        <span class="font-display font-medium text-stone-900 dark:text-stone-100" x-text="unit.code"></span>
                                        <span class="text-sm text-stone-500 dark:text-stone-400" x-text="' · ' + unit.area + ' m²'"></span>
                                    </div>
                                    <span
                                        class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium shrink-0"
                                        :class="{
                                            'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-300': unit.status === 'dostupno',
                                            'bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-300': unit.status === 'rezervirano',
                                            'bg-stone-200 dark:bg-stone-700 text-stone-700 dark:text-stone-300': unit.status === 'prodano',
                                        }"
                                        x-text="unit.statusLabel"
                                    ></span>
                                </a>
                            </template>
                        </div>

                        <p class="text-sm text-stone-500 dark:text-stone-400" x-show="active().units.length === 0">
                            {{ __('Na ovom katu još nema unesenih jedinica.') }}
                        </p>
                    </div>
                </template>

                <template x-if="!active()">
                    <p class="text-sm text-stone-500 dark:text-stone-400">
                        {{ __('Prijeđi mišem preko kata na slici (ili ga dodirni) da vidiš dostupne jedinice.') }}
                    </p>
                </template>
            </div>
        </div>
    </div>

    @if ($unassignedUnits->isNotEmpty())
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-16 sm:pb-20">
            <p class="text-xs font-semibold uppercase tracking-widest text-emerald-800 dark:text-emerald-400 mb-2">{{ __('Samostojeće') }}</p>
            <h2 class="font-display text-2xl font-semibold mb-8">{{ __('Jedinice') }}</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($unassignedUnits as $unit)
                    <x-unit-card :unit="$unit" />
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
                                + `class="${active ? 'fill-emerald-500/35 stroke-emerald-400' : 'fill-white/10 stroke-white/70 hover:fill-emerald-500/20'}" `
                                + `stroke-width="2"></polygon>`;
                        }
                        return html;
                    },
                };
            }
        </script>
    @endpush
@endonce
