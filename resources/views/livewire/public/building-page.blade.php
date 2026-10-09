<div>
    <x-page-hero :image="$facadeUrl">
        <p class="text-[13px] font-semibold text-ink-soft">
            <a href="{{ route('public.projects.show', $building->project) }}" wire:navigate class="transition hover:text-ink">{{ $building->project->name }}</a>
        </p>
        <h1 class="mt-3.5 font-display text-4xl sm:text-[46px] font-medium leading-tight text-ink">{{ $building->name }}</h1>
        <p class="mt-4 flex flex-wrap items-center gap-x-3 gap-y-2 text-sm text-ink-soft">
            <span class="inline-flex items-center rounded-full border border-line-strong bg-white/[0.08] px-3.5 py-1.5 text-[12.5px] font-semibold text-ink">{{ $building->type->label() }}</span>
            @if ($building->address)
                <span>{{ $building->address }}</span>
            @endif
        </p>
    </x-page-hero>

    <div
        class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-14 sm:py-16"
        x-data="buildingViewer({ floors: @js($floorsData), hasFacade: @js((bool) $facadeUrl) })"
        x-init="init()"
    >
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
            <div class="relative inline-block rounded-[22px] border border-line-strong shadow-2xl shadow-black/50 overflow-hidden bg-canvas-raised w-full">
                {{-- Fasada (bira kat hoverom) -- sakriva se čim aktivni kat ima svoj tlocrt --}}
                <template x-if="hasFacade">
                    <div x-show="!showPlan()">
                        <img x-ref="image" src="{{ $facadeUrl }}" @load="updateRect" class="block w-full h-auto select-none" draggable="false" alt="{{ $building->name }}">
                        <svg
                            x-ref="svg"
                            x-html="renderSvg()"
                            class="absolute inset-0 w-full h-full"
                            @mousemove="onMove($event)"
                            @click="onClick($event)"
                            style="cursor: pointer; filter: drop-shadow(0 0 1px rgba(0,0,0,.85));"
                        ></svg>
                    </div>
                </template>

                {{-- Tlocrt aktivnog kata (bira jedinicu hoverom) -- umjesto fasade dok god kat ima svoj tlocrt --}}
                <template x-if="showPlan()">
                    <div>
                        <img x-ref="planImage" :src="active().planUrl" @load="updatePlanRect" class="block w-full h-auto select-none" draggable="false" :alt="active().label">
                        <svg
                            x-ref="planSvg"
                            x-html="renderPlanSvg()"
                            class="absolute inset-0 w-full h-full"
                            @mousemove="onPlanMove($event)"
                            @mouseleave="hoveredUnitId = null"
                            @click="onPlanClick($event)"
                            style="cursor: pointer; filter: drop-shadow(0 0 1px rgba(0,0,0,.85));"
                        ></svg>
                    </div>
                </template>

                <template x-if="!hasFacade && !showPlan()">
                    <div class="aspect-[4/3] flex flex-col items-center justify-center gap-3 text-ink-faint text-sm p-8 text-center">
                        <x-building-placeholder-icon class="h-10 w-10" />
                        {{ __('Fasada objekta još nije dodana.') }}
                    </div>
                </template>
            </div>

            <div>
                <template x-if="floors.length > 1">
                    <div class="flex flex-wrap gap-2 mb-6">
                        <template x-for="floor in floors" :key="floor.id">
                            <button
                                type="button"
                                @mouseenter="select(floor.id)"
                                @click="select(floor.id)"
                                :class="floor.id === activeId ? 'bg-ink text-canvas font-bold' : 'bg-panel text-ink-soft hover:bg-white/10 font-semibold'"
                                class="px-[18px] py-2 rounded-full text-[13.5px] transition"
                                x-text="floor.label"
                            ></button>
                        </template>
                    </div>
                </template>

                <template x-if="active()">
                    <div>
                        <h2 class="font-display font-semibold text-[22px] mb-4 text-ink" x-text="active().label"></h2>

                        <p class="text-xs text-ink-faint mb-4" x-show="showPlan() && unitsWithShape().length > 0">
                            {{ __('Prijeđi mišem preko stana na tlocrtu (ili ga dodirni) za detalje.') }}
                        </p>

                        <div class="space-y-2.5" x-show="active().units.length > 0">
                            <template x-for="unit in active().units" :key="unit.id">
                                <a :href="unit.url" wire:navigate class="flex items-center justify-between gap-3 rounded-2xl border border-line bg-canvas-raised px-5 py-4 transition hover:-translate-y-0.5 hover:border-white/30">
                                    <div>
                                        <span class="font-display font-semibold text-base text-ink" x-text="unit.code"></span>
                                        <span class="text-[13px] text-ink-faint" x-text="' · ' + unit.area + ' m²'"></span>
                                    </div>
                                    <span
                                        class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold shrink-0"
                                        :class="{
                                            'bg-ink text-canvas': unit.status === 'dostupno',
                                            'border border-white/50 text-ink': unit.status === 'rezervirano',
                                            'bg-white/[0.06] border border-line text-ink-faint': unit.status === 'prodano',
                                        }"
                                        x-text="unit.statusLabel"
                                    ></span>
                                </a>
                            </template>
                        </div>

                        <p class="text-sm text-ink-soft" x-show="active().units.length === 0">
                            {{ __('Na ovom katu još nema unesenih jedinica.') }}
                        </p>
                    </div>
                </template>

                <template x-if="!active()">
                    <p class="text-sm text-ink-soft">
                        {{ __('Prijeđi mišem preko kata na slici (ili ga dodirni) da vidiš dostupne jedinice.') }}
                    </p>
                </template>
            </div>
        </div>
    </div>

    @if ($unassignedUnits->isNotEmpty())
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-16 sm:pb-20">
            <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-ink-faint mb-3">{{ __('Samostojeće') }}</p>
            <h2 class="font-display text-3xl font-medium text-ink mb-8">{{ __('Jedinice') }}</h2>
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
            function buildingViewer({ floors, hasFacade }) {
                return {
                    floors: floors.map(f => ({
                        ...f,
                        points: f.points || [],
                        units: f.units.map(u => ({ ...u, points: u.points || [] })),
                    })),
                    hasFacade,
                    activeId: null,
                    hoveredUnitId: null,
                    imgW: 0,
                    imgH: 0,
                    planW: 0,
                    planH: 0,

                    init() {
                        window.addEventListener('resize', () => {
                            this.updateRect();
                            this.updatePlanRect();
                        });

                        const withShape = this.floors.find(f => this.hasShape(f.points));
                        this.activeId = withShape ? withShape.id : (this.floors[0]?.id ?? null);
                        this.$nextTick(() => {
                            this.updateRect();
                            this.updatePlanRect();
                        });
                    },

                    showPlan() {
                        return !!(this.active() && this.active().planUrl);
                    },

                    updateRect() {
                        if (!this.$refs.image) return;
                        this.imgW = this.$refs.image.clientWidth;
                        this.imgH = this.$refs.image.clientHeight;
                    },

                    updatePlanRect() {
                        if (!this.$refs.planImage) return;
                        this.planW = this.$refs.planImage.clientWidth;
                        this.planH = this.$refs.planImage.clientHeight;
                    },

                    hasShape(points) {
                        return Array.isArray(points) && points.length >= 3;
                    },

                    toPx(point, w, h) {
                        return [(point[0] / 100) * w, (point[1] / 100) * h];
                    },

                    toSvgPoints(points, w, h) {
                        return points.map(p => this.toPx(p, w, h).join(',')).join(' ');
                    },

                    centroid(points) {
                        const x = points.reduce((s, p) => s + p[0], 0) / points.length;
                        const y = points.reduce((s, p) => s + p[1], 0) / points.length;
                        return [x, y];
                    },

                    escapeHtml(str) {
                        const div = document.createElement('div');
                        div.textContent = str ?? '';
                        return div.innerHTML;
                    },

                    select(id) {
                        this.activeId = id;
                        this.hoveredUnitId = null;
                        this.$nextTick(() => {
                            this.updateRect();
                            this.updatePlanRect();
                        });
                    },

                    active() {
                        return this.floors.find(f => f.id === this.activeId) ?? null;
                    },

                    unitsWithShape() {
                        const floor = this.active();
                        return floor ? floor.units.filter(u => this.hasShape(u.points)) : [];
                    },

                    pointInPolygon(x, y, points, w, h) {
                        let inside = false;
                        for (let i = 0, j = points.length - 1; i < points.length; j = i++) {
                            const [xi, yi] = this.toPx(points[i], w, h);
                            const [xj, yj] = this.toPx(points[j], w, h);
                            const intersect = ((yi > y) !== (yj > y)) && (x < (xj - xi) * (y - yi) / (yj - yi) + xi);
                            if (intersect) inside = !inside;
                        }
                        return inside;
                    },

                    floorAt(e) {
                        const rect = this.$refs.svg.getBoundingClientRect();
                        const x = e.clientX - rect.left;
                        const y = e.clientY - rect.top;
                        return this.floors.find(f => this.hasShape(f.points) && this.pointInPolygon(x, y, f.points, this.imgW, this.imgH));
                    },

                    onMove(e) {
                        const floor = this.floorAt(e);
                        if (floor) this.select(floor.id);
                    },

                    onClick(e) {
                        const floor = this.floorAt(e);
                        if (floor) this.select(floor.id);
                    },

                    renderSvg() {
                        let html = '';
                        for (const floor of this.floors) {
                            if (!this.hasShape(floor.points)) continue;
                            const active = floor.id === this.activeId;
                            html += `<polygon points="${this.toSvgPoints(floor.points, this.imgW, this.imgH)}" `
                                + `class="${active ? 'fill-white/30 stroke-white' : 'fill-white/[0.06] stroke-white/60 hover:fill-white/20'}" `
                                + `stroke-width="2"></polygon>`;
                        }
                        return html;
                    },

                    // --- Tlocrt aktivnog kata: hover/klik po stanovima ---

                    planUnitAt(e) {
                        const rect = this.$refs.planSvg.getBoundingClientRect();
                        const x = e.clientX - rect.left;
                        const y = e.clientY - rect.top;
                        return this.unitsWithShape().find(u => this.pointInPolygon(x, y, u.points, this.planW, this.planH));
                    },

                    onPlanMove(e) {
                        const unit = this.planUnitAt(e);
                        this.hoveredUnitId = unit ? unit.id : null;
                    },

                    onPlanClick(e) {
                        const unit = this.planUnitAt(e);
                        if (unit) window.Livewire.navigate(unit.url);
                    },

                    renderPlanSvg() {
                        let html = '';
                        for (const unit of this.unitsWithShape()) {
                            const active = unit.id === this.hoveredUnitId;
                            html += `<polygon points="${this.toSvgPoints(unit.points, this.planW, this.planH)}" `
                                + `class="${active ? 'fill-white/30 stroke-white' : 'fill-white/[0.06] stroke-white/60 hover:fill-white/20'}" `
                                + `stroke-width="2" style="cursor:pointer;"></polygon>`;

                            if (active) {
                                const c = this.toPx(this.centroid(unit.points), this.planW, this.planH);
                                const label = `${unit.code} · ${unit.statusLabel} · ${unit.area} m²`;
                                html += `<text x="${c[0]}" y="${c[1]}" text-anchor="middle" class="fill-white text-xs font-semibold pointer-events-none" `
                                    + `style="paint-order: stroke; stroke: rgba(0,0,0,.85); stroke-width: 3px;">${this.escapeHtml(label)}</text>`;
                            }
                        }
                        return html;
                    },
                };
            }
        </script>
    @endpush
@endonce
