<div>
    <x-page-hero>
        <p class="text-sm font-semibold text-brand-sky">
            <a href="{{ route('public.projects.show', $unit->building->project) }}" wire:navigate class="transition hover:text-white">{{ $unit->building->project->name }}</a>
            <span class="mx-1.5 text-white/40">/</span>
            <a href="{{ route('public.buildings.show', $unit->building) }}" wire:navigate class="transition hover:text-white">{{ $unit->building->name }}</a>
        </p>

        <div class="mt-3 flex flex-wrap items-center gap-3.5">
            <h1 class="text-4xl sm:text-5xl font-extrabold leading-tight tracking-tight">{{ __('Jedinica') }} {{ $unit->code }}</h1>
            <x-unit-status-badge :status="$unit->status" />
        </div>

        <p class="mt-4 flex flex-wrap items-center gap-x-2.5 gap-y-1 text-[15px] text-white/80">
            <span>{{ $unit->type->label() }}</span>
            <span class="text-white/40">&middot;</span>
            <span>{{ (float) $unit->area_m2 }} m²</span>
            @if ($unit->room_count)
                <span class="text-white/40">&middot;</span>
                <span>{{ trans_choice('{1}:count soba|[2,4]:count sobe|[5,*]:count soba', $unit->room_count, ['count' => $unit->room_count]) }}</span>
            @endif
            @if ($unit->floor)
                <span class="text-white/40">&middot;</span>
                <span>{{ $unit->floor->label }}</span>
            @endif
        </p>
    </x-page-hero>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-14">
        <div class="grid grid-cols-1 lg:grid-cols-[2fr_1fr] gap-10 lg:gap-12 items-start">
            <div>
                @if ($unit->description)
                    <p class="max-w-2xl text-base leading-[1.75] text-ink-soft mb-12">{{ $unit->description }}</p>
                @endif

                <div
                    x-data="unitPlanViewer({ rooms: @js($roomsData) })"
                    x-init="init()"
                >
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-brand mb-2">{{ __('Raspored prostorija') }}</p>
                    <h2 class="mb-8 text-3xl font-extrabold tracking-tight text-navy-900">{{ __('Tlocrt') }}</h2>

                    <div class="grid grid-cols-1 xl:grid-cols-[1.4fr_1fr] gap-7 items-start">
                        <div class="relative inline-block w-full overflow-hidden rounded-xl border border-line bg-white shadow-card">
                            @if ($planUrl)
                                <img x-ref="image" src="{{ $planUrl }}" @load="updateRect" class="block w-full h-auto select-none" draggable="false" alt="{{ __('Tlocrt jedinice') }} {{ $unit->code }}">
                                <svg
                                    x-ref="svg"
                                    x-html="renderSvg()"
                                    class="absolute inset-0 w-full h-full"
                                    @mousemove="onMove($event)"
                                    @mouseleave="hoveredId = null"
                                    @click="onClick($event)"
                                    style="cursor: pointer;"
                                ></svg>
                            @else
                                <div class="aspect-[4/3] flex flex-col items-center justify-center gap-3 p-8 text-center text-sm text-slate-300 bg-slate-100">
                                    <x-building-placeholder-icon class="h-12 w-12" />
                                    <span class="text-ink-soft">{{ __('Tlocrt jedinice još nije dodan.') }}</span>
                                </div>
                            @endif
                        </div>

                        <div>
                            <template x-if="rooms.length > 0">
                                <ul class="space-y-1">
                                    <template x-for="room in rooms" :key="room.id">
                                        <li
                                            @mouseenter="hoveredId = room.id"
                                            @mouseleave="hoveredId = null"
                                            @click="hoveredId = room.id"
                                            class="flex items-center justify-between gap-3 rounded-md border px-4 py-3 text-sm cursor-pointer transition"
                                            :class="hoveredId === room.id ? 'bg-brand-light border-brand/40 text-brand' : 'bg-white border-line text-ink hover:border-line-strong'"
                                        >
                                            <span x-text="room.label" class="font-semibold"></span>
                                            <span class="text-[13px] text-ink-soft" x-text="room.area ? room.area + ' m²' : ''"></span>
                                        </li>
                                    </template>
                                </ul>
                            </template>

                            <template x-if="rooms.length === 0">
                                <p class="text-sm text-ink-soft">{{ __('Detaljan raspored prostorija još nije unesen.') }}</p>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <aside class="lg:sticky lg:top-24">
                <div class="rounded-xl border border-line bg-white p-7 shadow-lift">
                    <span class="text-xs font-semibold uppercase tracking-wider text-ink-faint">{{ __('Cijena') }}</span>
                    @if ($unit->price)
                        <p class="mt-1 text-4xl font-extrabold leading-tight tracking-tight text-navy-900">{{ number_format((float) $unit->price, 0, ',', '.') }} €</p>
                        @if ((float) $unit->area_m2 > 0)
                            <p class="mt-1 text-sm text-ink-soft">≈ {{ number_format((float) $unit->price / (float) $unit->area_m2, 0, ',', '.') }} €/m²</p>
                        @endif
                    @else
                        <p class="mt-1 text-3xl font-extrabold text-navy-900">{{ __('Na upit') }}</p>
                    @endif

                    <dl class="mt-6 space-y-3 border-t border-line pt-5 text-sm">
                        <div class="flex justify-between"><dt class="text-ink-faint">{{ __('Tip') }}</dt><dd class="font-bold text-ink">{{ $unit->type->label() }}</dd></div>
                        <div class="flex justify-between"><dt class="text-ink-faint">{{ __('Površina') }}</dt><dd class="font-bold text-ink">{{ (float) $unit->area_m2 }} m²</dd></div>
                        @if ($unit->room_count)
                            <div class="flex justify-between"><dt class="text-ink-faint">{{ __('Sobe') }}</dt><dd class="font-bold text-ink">{{ $unit->room_count }}</dd></div>
                        @endif
                        @if ($unit->floor)
                            <div class="flex justify-between"><dt class="text-ink-faint">{{ __('Kat') }}</dt><dd class="font-bold text-ink">{{ $unit->floor->label }}</dd></div>
                        @endif
                        <div class="flex justify-between"><dt class="text-ink-faint">{{ __('Zgrada') }}</dt><dd class="font-bold text-ink">{{ $unit->building->name }}</dd></div>
                    </dl>

                    @php $investor = $unit->building->project->investor; @endphp
                    @if ($investor->contact_email || $investor->contact_phone)
                        <div class="mt-6 space-y-2.5">
                            @if ($investor->contact_email)
                                <a href="mailto:{{ $investor->contact_email }}?subject={{ rawurlencode(__('Upit za jedinicu').' '.$unit->code.' — '.$unit->building->project->name) }}" class="block rounded-md bg-brand py-3.5 text-center text-sm font-bold text-white transition hover:bg-brand-dark">{{ __('Zatraži informacije') }}</a>
                            @endif
                            @if ($investor->contact_phone)
                                <a href="tel:{{ preg_replace('/[^\d+]/', '', $investor->contact_phone) }}" class="block rounded-md border border-line-strong py-3.5 text-center text-sm font-bold text-navy-900 transition hover:border-brand hover:text-brand">{{ $investor->contact_phone }}</a>
                            @endif
                        </div>
                    @endif

                    <div class="mt-6 flex items-center gap-3 border-t border-line pt-5">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-brand-light text-sm font-extrabold text-brand">{{ mb_substr($investor->company_name, 0, 1) }}</span>
                        <div>
                            <p class="text-sm font-bold text-navy-900">{{ $investor->company_name }}</p>
                            <p class="text-xs text-ink-faint">{{ __('Investitor') }}</p>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</div>

@once
    @push('scripts')
        <script>
            function unitPlanViewer({ rooms }) {
                return {
                    rooms: rooms.map(r => ({ ...r, points: r.points || [] })),
                    hoveredId: null,
                    imgW: 0,
                    imgH: 0,

                    init() {
                        this.updateRect();
                        window.addEventListener('resize', () => this.updateRect());
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

                    roomAt(e) {
                        const rect = this.$refs.svg.getBoundingClientRect();
                        const x = e.clientX - rect.left;
                        const y = e.clientY - rect.top;
                        return this.rooms.find(r => this.hasShape(r.points) && this.pointInPolygon(x, y, r.points));
                    },

                    onMove(e) {
                        const room = this.roomAt(e);
                        this.hoveredId = room ? room.id : null;
                    },

                    onClick(e) {
                        const room = this.roomAt(e);
                        if (room) this.hoveredId = room.id;
                    },

                    renderSvg() {
                        let html = '';
                        for (const room of this.rooms) {
                            if (!this.hasShape(room.points)) continue;
                            const active = room.id === this.hoveredId;
                            html += `<polygon points="${this.toSvgPoints(room.points)}" `
                                + `class="${active ? 'fill-brand/30 stroke-brand' : 'fill-navy-900/5 stroke-navy-900/40'}" `
                                + `stroke-width="2"></polygon>`;
                            if (active) {
                                const c = this.toPx(this.centroid(room.points));
                                const label = room.area ? `${room.label} (${room.area} m²)` : room.label;
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
