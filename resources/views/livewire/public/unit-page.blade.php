<div>
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">
            <a href="{{ route('public.projects.show', $unit->building->project) }}" wire:navigate class="hover:underline">{{ $unit->building->project->name }}</a>
            /
            <a href="{{ route('public.buildings.show', $unit->building) }}" wire:navigate class="hover:underline">{{ $unit->building->name }}</a>
        </p>

        <div class="flex flex-wrap items-center gap-3">
            <h1 class="text-2xl sm:text-3xl font-bold">{{ __('Jedinica') }} {{ $unit->code }}</h1>
            <x-unit-status-badge :status="$unit->status" />
        </div>

        <p class="text-gray-500 dark:text-gray-400 mt-2">
            {{ $unit->type->label() }} &middot; {{ $unit->area_m2 }} m²
            @if ($unit->floor) &middot; {{ $unit->floor->label }} @endif
            @if ($unit->price) &middot; {{ number_format((float) $unit->price, 0, ',', '.') }} € @endif
        </p>

        @if ($unit->description)
            <p class="mt-6 max-w-2xl text-gray-700 dark:text-gray-300">{{ $unit->description }}</p>
        @endif
    </div>

    <div
        class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-16"
        x-data="unitPlanViewer({ rooms: @js($roomsData) })"
        x-init="init()"
    >
        <h2 class="text-lg font-semibold mb-6">{{ __('Tlocrt') }}</h2>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
            <div class="lg:col-span-2 relative inline-block border border-gray-200 dark:border-gray-800 rounded-lg overflow-hidden bg-gray-50 dark:bg-gray-900 w-full">
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
                    <div class="aspect-[4/3] flex items-center justify-center text-gray-400 dark:text-gray-600 text-sm p-8 text-center">
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
                                class="flex items-center justify-between gap-3 rounded-md px-3 py-2 text-sm cursor-pointer transition"
                                :class="hoveredId === room.id ? 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-900 dark:text-indigo-200' : 'hover:bg-gray-50 dark:hover:bg-gray-800'"
                            >
                                <span x-text="room.label"></span>
                                <span class="text-gray-500 dark:text-gray-400" x-text="room.area ? room.area + ' m²' : ''"></span>
                            </li>
                        </template>
                    </ul>
                </template>

                <template x-if="rooms.length === 0">
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Detaljan raspored prostorija još nije unesen.') }}</p>
                </template>
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
                                + `class="${active ? 'fill-indigo-500/35 stroke-indigo-600' : 'fill-white/10 stroke-white/70'}" `
                                + `stroke-width="2"></polygon>`;
                            if (active) {
                                const c = this.toPx(this.centroid(room.points));
                                const label = room.area ? `${room.label} (${room.area} m²)` : room.label;
                                html += `<text x="${c[0]}" y="${c[1]}" text-anchor="middle" class="fill-white text-xs font-semibold pointer-events-none" `
                                    + `style="paint-order: stroke; stroke: rgba(0,0,0,.6); stroke-width: 3px;">${this.escapeHtml(label)}</text>`;
                            }
                        }
                        return html;
                    },
                };
            }
        </script>
    @endpush
@endonce
