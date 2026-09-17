<div>
    <x-page-hero>
        <p class="text-sm text-emerald-300/90 mb-3">
            <a href="{{ route('public.projects.show', $unit->building->project) }}" wire:navigate class="hover:underline">{{ $unit->building->project->name }}</a>
            <span class="text-emerald-300/50">/</span>
            <a href="{{ route('public.buildings.show', $unit->building) }}" wire:navigate class="hover:underline">{{ $unit->building->name }}</a>
        </p>

        <div class="flex flex-wrap items-center gap-3">
            <h1 class="font-display text-3xl sm:text-5xl font-semibold leading-tight">{{ __('Jedinica') }} {{ $unit->code }}</h1>
            <x-unit-status-badge :status="$unit->status" />
        </div>

        <p class="mt-4 flex flex-wrap items-center gap-x-3 gap-y-1 text-emerald-100/80">
            <span>{{ $unit->type->label() }}</span>
            <span class="text-emerald-300/50">&middot;</span>
            <span>{{ $unit->area_m2 }} m²</span>
            @if ($unit->room_count)
                <span class="text-emerald-300/50">&middot;</span>
                <span>{{ trans_choice('{1}:count soba|[2,4]:count sobe|[5,*]:count soba', $unit->room_count, ['count' => $unit->room_count]) }}</span>
            @endif
            @if ($unit->floor)
                <span class="text-emerald-300/50">&middot;</span>
                <span>{{ $unit->floor->label }}</span>
            @endif
            @if ($unit->price)
                <span class="text-emerald-300/50">&middot;</span>
                <span class="font-display text-lg font-semibold text-white">{{ number_format((float) $unit->price, 0, ',', '.') }} €</span>
            @endif
        </p>
    </x-page-hero>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-14 sm:py-16">
        @if ($unit->description)
            <p class="max-w-2xl text-stone-600 dark:text-stone-400 leading-relaxed mb-14">{{ $unit->description }}</p>
        @endif

        <div
            x-data="unitPlanViewer({ rooms: @js($roomsData) })"
            x-init="init()"
        >
            <p class="text-xs font-semibold uppercase tracking-widest text-emerald-800 dark:text-emerald-400 mb-2">{{ __('Raspored prostorija') }}</p>
            <h2 class="font-display text-2xl sm:text-3xl font-semibold mb-8">{{ __('Tlocrt') }}</h2>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
                <div class="lg:col-span-2 relative inline-block rounded-2xl border border-stone-200 dark:border-stone-800 shadow-sm overflow-hidden bg-stone-100 dark:bg-stone-900 w-full">
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
                        <div class="aspect-[4/3] flex flex-col items-center justify-center gap-3 text-stone-400 dark:text-stone-600 text-sm p-8 text-center">
                            <x-building-placeholder-icon class="h-10 w-10" />
                            {{ __('Tlocrt jedinice još nije dodan.') }}
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
                                    class="flex items-center justify-between gap-3 rounded-lg px-3.5 py-2.5 text-sm cursor-pointer transition"
                                    :class="hoveredId === room.id ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-900 dark:text-emerald-200' : 'hover:bg-stone-100 dark:hover:bg-stone-800'"
                                >
                                    <span x-text="room.label" class="font-medium"></span>
                                    <span class="text-stone-500 dark:text-stone-400" x-text="room.area ? room.area + ' m²' : ''"></span>
                                </li>
                            </template>
                        </ul>
                    </template>

                    <template x-if="rooms.length === 0">
                        <p class="text-sm text-stone-500 dark:text-stone-400">{{ __('Detaljan raspored prostorija još nije unesen.') }}</p>
                    </template>
                </div>
            </div>
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
                                + `class="${active ? 'fill-emerald-500/35 stroke-emerald-600' : 'fill-stone-900/5 stroke-stone-900/40 dark:fill-white/10 dark:stroke-white/70'}" `
                                + `stroke-width="2"></polygon>`;
                            if (active) {
                                const c = this.toPx(this.centroid(room.points));
                                const label = room.area ? `${room.label} (${room.area} m²)` : room.label;
                                html += `<text x="${c[0]}" y="${c[1]}" text-anchor="middle" class="fill-white text-xs font-semibold pointer-events-none" `
                                    + `style="paint-order: stroke; stroke: rgba(6,78,59,.85); stroke-width: 3px;">${this.escapeHtml(label)}</text>`;
                            }
                        }
                        return html;
                    },
                };
            }
        </script>
    @endpush
@endonce
