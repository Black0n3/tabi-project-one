<div>
    <x-page-hero :image="$facadeUrl">
        <p class="text-sm font-semibold text-brand-sky">
            <a href="{{ route('public.projects.show', $building->project) }}" wire:navigate class="transition hover:text-white">{{ $building->project->name }}</a>
        </p>
        <h1 class="mt-2.5 text-4xl sm:text-5xl font-extrabold leading-tight tracking-tight">{{ $building->name }}</h1>
        <p class="mt-4 flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-white/80">
            <span class="inline-flex items-center rounded bg-brand px-2.5 py-1 text-[11px] font-bold uppercase tracking-wide text-white">{{ $building->type->label() }}</span>
            @if ($building->address)
                <span class="inline-flex items-center gap-1.5">
                    <svg viewBox="0 0 20 20" fill="none" class="h-4 w-4"><path d="M10 18s6-5.2 6-9.6A6 6 0 004 8.4C4 12.8 10 18 10 18z" stroke="currentColor" stroke-width="1.5"/><circle cx="10" cy="8.2" r="2" stroke="currentColor" stroke-width="1.5"/></svg>
                    {{ $building->address }}
                </span>
            @endif
        </p>
    </x-page-hero>

    <div
        class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-14"
        x-data="buildingViewer({ floors: @js($floorsData), hasFacade: @js((bool) $facadeUrl) })"
        x-init="init()"
    >
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
            <div class="relative inline-block w-full overflow-hidden rounded-xl border border-line bg-mist shadow-card">
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
                            style="cursor: pointer; filter: drop-shadow(0 0 1px rgba(0,0,0,.6));"
                        ></svg>
                    </div>
                </template>

                {{-- Tlocrt aktivnog kata (bira jedinicu hoverom) -- umjesto fasade dok god kat ima svoj tlocrt --}}
                <template x-if="showPlan()">
                    <div class="bg-white">
                        <img x-ref="planImage" :src="active().planUrl" @load="updatePlanRect" class="block w-full h-auto select-none" draggable="false" :alt="active().label">
                        <svg
                            x-ref="planSvg"
                            x-html="renderPlanSvg()"
                            class="absolute inset-0 w-full h-full"
                            @mousemove="onPlanMove($event)"
                            @mouseleave="hoveredUnitId = null"
                            @click="onPlanClick($event)"
                            style="cursor: pointer;"
                        ></svg>
                    </div>
                </template>

                <template x-if="!hasFacade && !showPlan()">
                    <div class="aspect-[4/3] flex flex-col items-center justify-center gap-3 text-slate-300 text-sm p-8 text-center bg-slate-100">
                        <x-building-placeholder-icon class="h-12 w-12" />
                        <span class="text-ink-soft">{{ __('Fasada objekta još nije dodana.') }}</span>
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
                                :class="floor.id === activeId ? 'bg-brand text-white border-brand' : 'bg-white text-ink border-line-strong hover:border-brand hover:text-brand'"
                                class="px-4 py-2 rounded-md border text-[13.5px] font-bold transition"
                                x-text="floor.label"
                            ></button>
                        </template>
                    </div>
                </template>

                <template x-if="active()">
                    <div>
                        <h2 class="mb-4 text-2xl font-extrabold tracking-tight text-navy-900" x-text="active().label"></h2>

                        <p class="mb-4 text-xs text-ink-faint" x-show="showPlan() && unitsWithShape().length > 0">
                            {{ __('Prijeđi mišem preko stana na tlocrtu (ili ga dodirni) za detalje.') }}
                        </p>

                        <div class="space-y-2.5" x-show="active().units.length > 0">
                            <template x-for="unit in active().units" :key="unit.id">
                                <a :href="unit.url" wire:navigate class="flex items-center justify-between gap-3 rounded-lg border border-line bg-white px-5 py-4 shadow-card transition hover:-translate-y-0.5 hover:border-brand/50 hover:shadow-lift">
                                    <div>
                                        <span class="text-base font-extrabold text-navy-900" x-text="unit.code"></span>
                                        <span class="text-[13px] text-ink-soft" x-text="' · ' + unit.area + ' m²'"></span>
                                    </div>
                                    <span
                                        class="inline-flex items-center rounded px-2.5 py-1 text-[11px] font-bold uppercase tracking-wide shrink-0"
                                        :class="{
                                            'bg-emerald-600 text-white': unit.status === 'dostupno',
                                            'bg-amber-500 text-navy-950': unit.status === 'rezervirano',
                                            'bg-slate-500 text-white': unit.status === 'prodano',
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
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-brand mb-2">{{ __('Samostojeće') }}</p>
            <h2 class="mb-8 text-3xl font-extrabold tracking-tight text-navy-900">{{ __('Jedinice') }}</h2>
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
                                + `class="${active ? 'fill-brand/40 stroke-white' : 'fill-white/10 stroke-white/80 hover:fill-white/25'}" `
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
                                + `class="${active ? 'fill-brand/30 stroke-brand' : 'fill-navy-900/5 stroke-navy-900/40 hover:fill-brand/15'}" `
                                + `stroke-width="2" style="cursor:pointer;"></polygon>`;

                            if (active) {
                                const c = this.toPx(this.centroid(unit.points), this.planW, this.planH);
                                const label = `${unit.code} · ${unit.statusLabel} · ${unit.area} m²`;
                                html += `<text x="${c[0]}" y="${c[1]}" text-anchor="middle" class="fill-white text-xs font-semibold pointer-events-none" `
                                    + `style="paint-order: stroke; stroke: rgba(10,30,69,.9); stroke-width: 3px;">${this.escapeHtml(label)}</text>`;
                            }
                        }
                        return html;
                    },
                };
            }
        </script>
    @endpush
@endonce
