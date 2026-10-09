<div>
    <x-page-hero>
        <p class="text-[13px] text-ink-soft">
            <a href="{{ route('public.projects.show', $unit->building->project) }}" wire:navigate class="transition hover:text-ink">{{ $unit->building->project->name }}</a>
            <span class="mx-1.5 text-ink-faint">/</span>
            <a href="{{ route('public.buildings.show', $unit->building) }}" wire:navigate class="transition hover:text-ink">{{ $unit->building->name }}</a>
        </p>

        <div class="mt-3.5 flex flex-wrap items-center gap-3.5">
            <h1 class="font-display text-4xl sm:text-[44px] font-medium leading-tight text-ink">{{ __('Jedinica') }} {{ $unit->code }}</h1>
            <x-unit-status-badge :status="$unit->status" />
        </div>

        <p class="mt-4 flex flex-wrap items-center gap-x-2.5 gap-y-1 text-[14.5px] text-ink-soft">
            <span>{{ $unit->type->label() }}</span>
            <span class="text-ink-faint">&middot;</span>
            <span>{{ $unit->area_m2 }} m²</span>
            @if ($unit->room_count)
                <span class="text-ink-faint">&middot;</span>
                <span>{{ trans_choice('{1}:count soba|[2,4]:count sobe|[5,*]:count soba', $unit->room_count, ['count' => $unit->room_count]) }}</span>
            @endif
            @if ($unit->floor)
                <span class="text-ink-faint">&middot;</span>
                <span>{{ $unit->floor->label }}</span>
            @endif
        </p>
    </x-page-hero>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-14 sm:py-16">
        <div class="grid grid-cols-1 lg:grid-cols-[2fr_1fr] gap-10 lg:gap-12 items-start">
            <div>
                @if ($unit->description)
                    <p class="max-w-2xl text-[15.5px] leading-[1.7] text-ink-soft mb-12">{{ $unit->description }}</p>
                @endif

                <div
                    x-data="unitPlanViewer({ rooms: @js($roomsData) })"
                    x-init="init()"
                >
                    <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-ink-faint mb-3">{{ __('Raspored prostorija') }}</p>
                    <h2 class="font-display text-3xl font-medium text-ink mb-8">{{ __('Tlocrt') }}</h2>

                    <div class="grid grid-cols-1 xl:grid-cols-[1.4fr_1fr] gap-7 items-start">
                        <div class="relative inline-block rounded-[20px] border border-line-strong shadow-2xl shadow-black/50 overflow-hidden bg-canvas-raised w-full">
                            @if ($planUrl)
                                <img x-ref="image" src="{{ $planUrl }}" @load="updateRect" class="block w-full h-auto select-none" draggable="false" alt="{{ __('Tlocrt jedinice') }} {{ $unit->code }}">
                                <svg
                                    x-ref="svg"
                                    x-html="renderSvg()"
                                    class="absolute inset-0 w-full h-full"
                                    @mousemove="onMove($event)"
                                    @mouseleave="hoveredId = null"
                                    @click="onClick($event)"
                                    style="cursor: pointer; filter: drop-shadow(0 0 1px rgba(0,0,0,.85));"
                                ></svg>
                            @else
                                <div class="aspect-[4/3] flex flex-col items-center justify-center gap-3 text-ink-faint text-sm p-8 text-center">
                                    <x-building-placeholder-icon class="h-10 w-10" />
                                    {{ __('Tlocrt jedinice još nije dodan.') }}
                                </div>
                            @endif
                        </div>

                        <div>
                            <template x-if="rooms.length > 0">
                                <ul class="space-y-0.5">
                                    <template x-for="room in rooms" :key="room.id">
                                        <li
                                            @mouseenter="hoveredId = room.id"
                                            @mouseleave="hoveredId = null"
                                            @click="hoveredId = room.id"
                                            class="flex items-center justify-between gap-3 rounded-[10px] px-3.5 py-2.5 text-sm cursor-pointer transition border"
                                            :class="hoveredId === room.id ? 'bg-white/[0.08] border-line-strong text-ink' : 'border-transparent text-ink hover:bg-white/[0.05]'"
                                        >
                                            <span x-text="room.label" class="font-medium"></span>
                                            <span class="text-[13px] text-ink-faint" x-text="room.area ? room.area + ' m²' : ''"></span>
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

            <aside class="lg:sticky lg:top-28">
                <div class="rounded-[22px] border border-line-strong bg-panel-strong p-7 shadow-2xl shadow-black/50">
                    <span class="text-xs text-ink-faint">{{ __('Cijena') }}</span>
                    @if ($unit->price)
                        <p class="mt-1 font-display text-[34px] font-semibold leading-tight text-ink">{{ number_format((float) $unit->price, 0, ',', '.') }} €</p>
                        @if ((float) $unit->area_m2 > 0)
                            <p class="mt-1 text-[12.5px] text-ink-soft">≈ {{ number_format((float) $unit->price / (float) $unit->area_m2, 0, ',', '.') }} €/m²</p>
                        @endif
                    @else
                        <p class="mt-1 font-display text-2xl font-semibold text-ink">{{ __('Na upit') }}</p>
                    @endif

                    <dl class="mt-6 space-y-3 border-t border-line pt-5 text-[13.5px]">
                        <div class="flex justify-between"><dt class="text-ink-faint">{{ __('Tip') }}</dt><dd class="font-semibold text-ink">{{ $unit->type->label() }}</dd></div>
                        <div class="flex justify-between"><dt class="text-ink-faint">{{ __('Površina') }}</dt><dd class="font-semibold text-ink">{{ $unit->area_m2 }} m²</dd></div>
                        @if ($unit->room_count)
                            <div class="flex justify-between"><dt class="text-ink-faint">{{ __('Sobe') }}</dt><dd class="font-semibold text-ink">{{ $unit->room_count }}</dd></div>
                        @endif
                        @if ($unit->floor)
                            <div class="flex justify-between"><dt class="text-ink-faint">{{ __('Kat') }}</dt><dd class="font-semibold text-ink">{{ $unit->floor->label }}</dd></div>
                        @endif
                        <div class="flex justify-between"><dt class="text-ink-faint">{{ __('Zgrada') }}</dt><dd class="font-semibold text-ink">{{ $unit->building->name }}</dd></div>
                    </dl>

                    @php $investor = $unit->building->project->investor; @endphp
                    @if ($investor->contact_email || $investor->contact_phone)
                        <div class="mt-6 space-y-2.5">
                            @if ($investor->contact_email)
                                <a href="mailto:{{ $investor->contact_email }}?subject={{ rawurlencode(__('Upit za jedinicu').' '.$unit->code.' — '.$unit->building->project->name) }}" class="block rounded-full bg-ink py-3.5 text-center text-sm font-bold text-canvas transition hover:brightness-90">{{ __('Zatraži informacije') }}</a>
                            @endif
                            @if ($investor->contact_phone)
                                <a href="tel:{{ preg_replace('/[^\d+]/', '', $investor->contact_phone) }}" class="block rounded-full border border-line-strong py-3.5 text-center text-sm font-semibold text-ink transition hover:bg-white/10">{{ $investor->contact_phone }}</a>
                            @endif
                        </div>
                    @endif

                    <div class="mt-6 flex items-center gap-3 border-t border-line pt-5">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full border border-line-strong bg-canvas-raised font-display text-sm font-semibold text-ink">{{ mb_substr($investor->company_name, 0, 1) }}</span>
                        <div>
                            <p class="text-[13px] font-semibold text-ink">{{ $investor->company_name }}</p>
                            <p class="text-[11.5px] text-ink-faint">{{ __('Investitor') }}</p>
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
                                + `class="${active ? 'fill-white/30 stroke-white' : 'fill-white/[0.06] stroke-white/60'}" `
                                + `stroke-width="2"></polygon>`;
                            if (active) {
                                const c = this.toPx(this.centroid(room.points));
                                const label = room.area ? `${room.label} (${room.area} m²)` : room.label;
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
