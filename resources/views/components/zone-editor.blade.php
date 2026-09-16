@props([
    'image' => null,
    'zones' => [], // array of ['id' => int, 'label' => string, 'points' => array|null]
    'saveMethod' => 'saveZone',
    'deleteMethod' => 'deleteZone',
    'newLabelPlaceholder' => 'Naziv',
    'emptyImageMessage' => 'Prvo dodaj sliku da bi mogao/la crtati zone.',
])

<div
    x-data="zoneEditor({
        zones: @js(collect($zones)->map(fn ($z) => ['id' => $z['id'], 'label' => $z['label'], 'points' => $z['points'] ?? []])->values()),
        saveMethod: @js($saveMethod),
        deleteMethod: @js($deleteMethod),
    })"
    x-init="init()"
>
    @if (! $image)
        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $emptyImageMessage }}</p>
    @else
        <div class="flex flex-col lg:flex-row gap-6">
            <div class="relative inline-block border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden bg-gray-50 dark:bg-gray-900 max-w-full">
                <img x-ref="image" src="{{ $image }}" @load="onImageLoad" class="block max-w-full h-auto select-none" draggable="false" alt="">

                {{-- Alpine's x-for/x-if templates don't work reliably inside <svg>, so the
                     zone shapes are rendered as an HTML string via x-html and interactions
                     are handled through event delegation on the <svg> root instead. --}}
                <svg
                    x-ref="svg"
                    x-html="renderSvg()"
                    class="absolute inset-0 w-full h-full"
                    :style="drawing ? 'cursor: crosshair;' : 'cursor: default;'"
                    @click="onSvgClick($event)"
                    @dblclick="onSvgDblClick($event)"
                    @mousedown="onSvgMouseDown($event)"
                    @mousemove="onDrag($event)"
                    @mouseup="stopDrag()"
                    @mouseleave="stopDrag()"
                ></svg>
            </div>

            <div class="w-full lg:w-72 shrink-0 space-y-4">
                <template x-if="!drawing && !selectedZoneId">
                    <div>
                        <button type="button" @click="startDrawing()" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white">
                            + {{ __('Nacrtaj novu zonu') }}
                        </button>

                        <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">{{ __('Klikni na postojeću zonu za uređivanje ili brisanje.') }}</p>
                    </div>
                </template>

                <template x-if="drawing">
                    <div class="space-y-3">
                        <p class="text-sm text-gray-700 dark:text-gray-300">
                            {{ __('Klikni po slici da dodaš točke (min. 3).') }}
                            <span x-text="newPoints.length"></span> {{ __('točaka.') }}
                        </p>

                        <div class="flex gap-2">
                            <button type="button" @click="undoPoint()" :disabled="newPoints.length === 0" class="inline-flex items-center px-3 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest disabled:opacity-25">
                                {{ __('Poništi točku') }}
                            </button>
                            <button type="button" @click="cancelDrawing()" class="inline-flex items-center px-3 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest">
                                {{ __('Odustani') }}
                            </button>
                        </div>

                        <template x-if="newPoints.length >= 3">
                            <div class="pt-3 border-t border-gray-200 dark:border-gray-700 space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Poveži s postojećim') }}</label>
                                    <select x-model="attachToId" class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm text-sm">
                                        <option value="">{{ __('— nova stavka —') }}</option>
                                        <template x-for="zone in zones.filter(z => !hasShape(z.points))" :key="'opt-'+zone.id">
                                            <option :value="zone.id" x-text="zone.label"></option>
                                        </template>
                                    </select>
                                </div>

                                <div x-show="attachToId === ''">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Ili unesi naziv za novu stavku') }}</label>
                                    <input x-model="newLabel" type="text" placeholder="{{ $newLabelPlaceholder }}" class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm text-sm">
                                </div>

                                <button type="button" @click="save()" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                                    {{ __('Spremi zonu') }}
                                </button>
                            </div>
                        </template>
                    </div>
                </template>

                <template x-if="selectedZoneId && !drawing">
                    <div class="space-y-3">
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200" x-text="selectedZone()?.label"></p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ __('Povuci točku da je pomakneš, klikni na rub oblika da dodaš novu točku, dvoklikni na točku da je ukloniš.') }}
                            <span x-text="editPoints.length"></span> {{ __('točaka.') }}
                        </p>

                        <div class="flex flex-wrap gap-2">
                            <button type="button" @click="saveEdited()" class="inline-flex items-center px-3 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                                {{ __('Spremi promjene') }}
                            </button>
                            <button type="button" @click="deleteSelected()" class="inline-flex items-center px-3 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500">
                                {{ __('Obriši zonu') }}
                            </button>
                            <button type="button" @click="deselect()" class="inline-flex items-center px-3 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest">
                                {{ __('Zatvori') }}
                            </button>
                        </div>
                    </div>
                </template>

                <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                    <p class="text-xs font-semibold uppercase text-gray-400 dark:text-gray-500 mb-2">{{ __('Sve stavke') }}</p>
                    <ul class="space-y-1 text-sm">
                        <template x-for="zone in zones" :key="'list-'+zone.id">
                            <li class="flex items-center justify-between gap-2">
                                <span x-text="zone.label" class="text-gray-700 dark:text-gray-300"></span>
                                <span
                                    x-text="hasShape(zone.points) ? '{{ __('označeno') }}' : '{{ __('bez zone') }}'"
                                    :class="hasShape(zone.points) ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-400'"
                                    class="text-xs shrink-0"
                                ></span>
                            </li>
                        </template>
                    </ul>
                </div>
            </div>
        </div>
    @endif
</div>

@once
    @push('scripts')
        <script>
            function zoneEditor({ zones, saveMethod, deleteMethod }) {
                return {
                    zones: zones.map(z => ({ ...z, points: z.points || [] })),
                    drawing: false,
                    newPoints: [],
                    attachToId: '',
                    newLabel: '',
                    selectedZoneId: null,
                    editPoints: [],
                    draggingIndex: null,
                    didDrag: false,
                    imgW: 0,
                    imgH: 0,

                    init() {
                        this.updateRect();
                        window.addEventListener('resize', () => this.updateRect());
                    },

                    onImageLoad() {
                        this.updateRect();
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
                        if (!points.length) return [0, 0];
                        const x = points.reduce((s, p) => s + p[0], 0) / points.length;
                        const y = points.reduce((s, p) => s + p[1], 0) / points.length;
                        return [x, y];
                    },

                    escapeHtml(str) {
                        const div = document.createElement('div');
                        div.textContent = str ?? '';
                        return div.innerHTML;
                    },

                    // Builds the inner SVG markup by hand (instead of Alpine x-for/x-if
                    // templates, which don't work reliably inside <svg>) and injects it
                    // via x-html. Clicks/drags on the generated shapes are picked up
                    // through event delegation on the <svg> root (onSvgClick/onSvgMouseDown).
                    renderSvg() {
                        let html = '';

                        for (const zone of this.zones) {
                            if (!this.hasShape(zone.points) || zone.id === this.selectedZoneId) continue;

                            const c = this.toPx(this.centroid(zone.points));
                            html += `<g>`
                                + `<polygon data-zone-id="${zone.id}" points="${this.toSvgPoints(zone.points)}" `
                                + `class="fill-emerald-500/25 stroke-emerald-600 hover:fill-emerald-500/40" stroke-width="2" style="cursor:pointer;"></polygon>`
                                + `<text x="${c[0]}" y="${c[1]}" text-anchor="middle" class="fill-white text-xs font-semibold pointer-events-none" `
                                + `style="paint-order: stroke; stroke: rgba(0,0,0,.6); stroke-width: 3px;">${this.escapeHtml(zone.label)}</text>`
                                + `</g>`;
                        }

                        if (this.selectedZoneId && !this.drawing) {
                            html += `<g><polygon points="${this.toSvgPoints(this.editPoints)}" class="fill-indigo-500/30 stroke-indigo-600" stroke-width="2"></polygon>`;
                            this.editPoints.forEach((p, index) => {
                                const px = this.toPx(p);
                                html += `<circle data-vertex-index="${index}" cx="${px[0]}" cy="${px[1]}" r="7" `
                                    + `fill="#4f46e5" stroke="white" stroke-width="1.5" style="cursor:grab;"></circle>`;
                            });
                            html += `</g>`;
                        }

                        if (this.drawing) {
                            const canClose = this.newPoints.length >= 3;
                            const previewPoints = canClose ? [...this.newPoints, this.newPoints[0]] : this.newPoints;
                            html += `<polyline points="${this.toSvgPoints(previewPoints)}" fill="none" stroke="#f59e0b" stroke-width="2" stroke-dasharray="4"></polyline>`;
                            this.newPoints.forEach((p, index) => {
                                const px = this.toPx(p);
                                const isStart = canClose && index === 0;
                                html += isStart
                                    ? `<circle cx="${px[0]}" cy="${px[1]}" r="8" fill="#f59e0b" stroke="white" stroke-width="2" style="cursor:pointer;"></circle>`
                                    : `<circle cx="${px[0]}" cy="${px[1]}" r="4" fill="#f59e0b"></circle>`;
                            });
                        }

                        return html;
                    },

                    posFromEvent(e) {
                        const rect = this.$refs.svg.getBoundingClientRect();
                        let x = ((e.clientX - rect.left) / rect.width) * 100;
                        let y = ((e.clientY - rect.top) / rect.height) * 100;
                        x = Math.max(0, Math.min(100, x));
                        y = Math.max(0, Math.min(100, y));
                        return [Math.round(x * 100) / 100, Math.round(y * 100) / 100];
                    },

                    startDrawing() {
                        this.drawing = true;
                        this.newPoints = [];
                        this.attachToId = '';
                        this.newLabel = '';
                        this.selectedZoneId = null;
                    },

                    cancelDrawing() {
                        this.drawing = false;
                        this.newPoints = [];
                    },

                    undoPoint() {
                        this.newPoints.pop();
                    },

                    onSvgClick(e) {
                        if (this.draggingIndex !== null) return;

                        // A click fires right after mouseup, even when that mouseup
                        // ended a drag -- ignore it so releasing a dragged vertex
                        // doesn't also insert/select something under the cursor.
                        if (this.didDrag) {
                            this.didDrag = false;
                            return;
                        }

                        if (this.drawing) {
                            const point = this.posFromEvent(e);

                            // Clicking back near the starting point closes the shape
                            // instead of adding a stray extra vertex there -- the
                            // closing edge is already drawn in the preview and on save.
                            if (this.newPoints.length >= 3 && this.isNearPoint(point, this.newPoints[0])) {
                                return;
                            }

                            this.newPoints.push(point);
                            return;
                        }

                        if (this.selectedZoneId) {
                            // Clicking directly on a vertex is for dragging (or
                            // double-click to remove), not for inserting a new point.
                            if (e.target.closest('[data-vertex-index]')) return;

                            this.insertPointOnNearestEdge(this.posFromEvent(e));
                            return;
                        }

                        const zoneEl = e.target.closest('[data-zone-id]');
                        if (zoneEl) {
                            const zone = this.zones.find(z => z.id === parseInt(zoneEl.dataset.zoneId));
                            if (zone) this.selectZone(zone);
                        }
                    },

                    onSvgDblClick(e) {
                        if (!this.selectedZoneId) return;

                        const vertexEl = e.target.closest('[data-vertex-index]');
                        if (!vertexEl) return;

                        // A shape needs at least 3 points to stay a valid polygon --
                        // delete the whole zone instead if you need fewer.
                        if (this.editPoints.length <= 3) return;

                        this.editPoints.splice(parseInt(vertexEl.dataset.vertexIndex), 1);
                    },

                    // Distance check in screen pixels (not raw percentage points) so the
                    // "click to close"/"click to insert" tolerance stays consistent
                    // regardless of image size.
                    isNearPoint(a, b, toleranceInPx = 12) {
                        const [ax, ay] = this.toPx(a);
                        const [bx, by] = this.toPx(b);
                        return Math.hypot(ax - bx, ay - by) <= toleranceInPx;
                    },

                    distanceToSegment(p, a, b) {
                        const [px, py] = p, [ax, ay] = a, [bx, by] = b;
                        const dx = bx - ax, dy = by - ay;
                        const lengthSq = dx * dx + dy * dy;
                        let t = lengthSq === 0 ? 0 : ((px - ax) * dx + (py - ay) * dy) / lengthSq;
                        t = Math.max(0, Math.min(1, t));
                        return Math.hypot(px - (ax + t * dx), py - (ay + t * dy));
                    },

                    // Inserts a new vertex right after whichever edge of the currently
                    // edited shape the click landed closest to (within tolerance), so
                    // clicking on the shape's outline adds a point there rather than
                    // requiring the whole zone to be redrawn from scratch.
                    insertPointOnNearestEdge(point) {
                        const px = this.toPx(point);
                        let bestIndex = -1;
                        let bestDistance = Infinity;

                        for (let i = 0; i < this.editPoints.length; i++) {
                            const a = this.toPx(this.editPoints[i]);
                            const b = this.toPx(this.editPoints[(i + 1) % this.editPoints.length]);
                            const distance = this.distanceToSegment(px, a, b);
                            if (distance < bestDistance) {
                                bestDistance = distance;
                                bestIndex = i;
                            }
                        }

                        if (bestIndex === -1 || bestDistance > 14) return;

                        this.editPoints.splice(bestIndex + 1, 0, point);
                    },

                    onSvgMouseDown(e) {
                        const vertexEl = e.target.closest('[data-vertex-index]');
                        if (vertexEl) {
                            this.draggingIndex = parseInt(vertexEl.dataset.vertexIndex);
                            this.didDrag = false;
                        }
                    },

                    async save() {
                        if (this.newPoints.length < 3) return;
                        const targetId = this.attachToId ? parseInt(this.attachToId) : null;
                        if (!targetId && !this.newLabel.trim()) return;

                        const saved = await this.$wire.call(saveMethod, targetId, targetId ? null : this.newLabel.trim(), this.newPoints);

                        if (saved) {
                            const existing = this.zones.find(z => z.id === saved.id);
                            if (existing) {
                                existing.points = saved.points;
                            } else {
                                this.zones.push(saved);
                            }
                        }

                        this.drawing = false;
                        this.newPoints = [];
                    },

                    selectZone(zone) {
                        if (this.drawing) return;
                        if (!this.hasShape(zone.points)) return;
                        this.selectedZoneId = zone.id;
                        this.editPoints = zone.points.map(p => [...p]);
                    },

                    selectedZone() {
                        return this.zones.find(z => z.id === this.selectedZoneId);
                    },

                    deselect() {
                        this.selectedZoneId = null;
                        this.editPoints = [];
                    },

                    onDrag(e) {
                        if (this.draggingIndex === null) return;
                        this.didDrag = true;
                        this.editPoints[this.draggingIndex] = this.posFromEvent(e);
                    },

                    stopDrag() {
                        this.draggingIndex = null;
                    },

                    async saveEdited() {
                        if (!this.selectedZoneId) return;

                        await this.$wire.call(saveMethod, this.selectedZoneId, null, this.editPoints);
                        const z = this.zones.find(z => z.id === this.selectedZoneId);
                        if (z) z.points = this.editPoints.map(p => [...p]);
                        this.deselect();
                    },

                    async deleteSelected() {
                        if (!this.selectedZoneId) return;
                        if (!confirm('{{ __('Obrisati ovu zonu?') }}')) return;
                        await this.$wire.call(deleteMethod, this.selectedZoneId);
                        const z = this.zones.find(z => z.id === this.selectedZoneId);
                        if (z) z.points = [];
                        this.deselect();
                    },
                };
            }
        </script>
    @endpush
@endonce
