@props([
    'image' => null,
    'zones' => [], // array of ['id' => int, 'label' => string, 'points' => array|null]
    'saveMethod' => 'saveZone',
    'deleteMethod' => 'deleteZone',
    'newLabelPlaceholder' => 'Naziv',
    'emptyImageMessage' => 'Prvo dodaj sliku da bi mogao/la crtati zone.',
])

{{-- wire:ignore: all editor state lives client-side (zones are pushed into `zones` after a
     save). Without it, Livewire's re-render after $wire.call() morphs the DOM and detaches
     Alpine's x-if templates, so the drawing panel never appears for the next zone. --}}
<div
    wire:ignore
    x-data="zoneEditor({
        zones: @js(collect($zones)->map(fn ($z) => ['id' => $z['id'], 'label' => $z['label'], 'points' => $z['points'] ?? []])->values()),
        saveMethod: @js($saveMethod),
        deleteMethod: @js($deleteMethod),
    })"
    x-init="init()"
>
    @if (! $image)
        <p class="text-sm text-ink-soft">{{ $emptyImageMessage }}</p>
    @else
        <div class="flex flex-col lg:flex-row gap-6">
            <div class="relative inline-block border border-line rounded-lg overflow-hidden bg-mist max-w-full">
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
                    @mousemove="onSvgMouseMove($event)"
                    @mouseup="stopDrag()"
                    @mouseleave="stopDrag(); hoveredZoneId = null; cursorPoint = null; snapTarget = null"
                ></svg>
            </div>

            <div class="w-full lg:w-72 shrink-0 space-y-4">
                <template x-if="!drawing && !selectedZoneId">
                    <div>
                        <button type="button" @click="startDrawing()" class="inline-flex items-center px-4 py-2 bg-brand border border-transparent rounded-md font-bold text-sm text-white hover:bg-brand-dark">
                            + {{ __('Nacrtaj novu zonu') }}
                        </button>

                        <p class="mt-3 text-xs text-ink-soft">{{ __('Klikni na postojeću zonu za uređivanje ili brisanje.') }}</p>
                    </div>
                </template>

                <template x-if="drawing">
                    <div class="space-y-3">
                        <p class="text-sm text-ink">
                            <span x-show="!drawingClosed">{{ __('Klikni po slici da dodaš točke. Kad završiš, klikni na prvu točku da zatvoriš oblik (min. 3 točke). Točke se same hvataju za rubove i uglove susjednih zona (drži Shift za slobodno postavljanje).') }}</span>
                            <span x-show="drawingClosed">{{ __('Oblik je zatvoren. Odaberi naziv i spremi ili poništi točku za nastavak crtanja.') }}</span>
                            <span x-text="newPoints.length"></span> {{ __('točaka.') }}
                        </p>

                        <div class="flex gap-2">
                            <button type="button" @click="undoPoint()" :disabled="newPoints.length === 0" class="inline-flex items-center px-3 py-2 bg-white border border-line-strong rounded-md font-bold text-sm text-ink disabled:opacity-25">
                                {{ __('Poništi točku') }}
                            </button>
                            <button type="button" @click="cancelDrawing()" class="inline-flex items-center px-3 py-2 bg-white border border-line-strong rounded-md font-bold text-sm text-ink">
                                {{ __('Odustani') }}
                            </button>
                        </div>

                        <template x-if="drawingClosed">
                            <div class="pt-3 border-t border-line space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-ink mb-1">{{ __('Poveži s postojećim') }}</label>
                                    <select x-model="attachToId" class="block w-full border-line-strong rounded-md shadow-sm text-sm">
                                        <option value="">{{ __('— nova stavka —') }}</option>
                                        <template x-for="zone in zones.filter(z => !hasShape(z.points))" :key="'opt-'+zone.id">
                                            <option :value="zone.id" x-text="zone.label"></option>
                                        </template>
                                    </select>
                                </div>

                                <div x-show="attachToId === ''">
                                    <label class="block text-sm font-medium text-ink mb-1">{{ __('Ili unesi naziv za novu stavku') }}</label>
                                    <input x-model="newLabel" type="text" placeholder="{{ $newLabelPlaceholder }}" class="block w-full border-line-strong rounded-md shadow-sm text-sm">
                                </div>

                                <button type="button" @click="save()" class="inline-flex items-center px-4 py-2 bg-brand border border-transparent rounded-md font-bold text-sm text-white hover:bg-brand-dark">
                                    {{ __('Spremi zonu') }}
                                </button>
                            </div>
                        </template>
                    </div>
                </template>

                <template x-if="selectedZoneId && !drawing">
                    <div class="space-y-3">
                        <p class="text-sm font-medium text-navy-900" x-text="selectedZone()?.label"></p>
                        <p class="text-xs text-ink-soft">
                            {{ __('Povuci točku da je pomakneš (hvata se za susjedne zone, Shift za slobodno), klikni na rub oblika da dodaš novu točku, dvoklikni na točku da je ukloniš.') }}
                            <span x-text="editPoints.length"></span> {{ __('točaka.') }}
                        </p>

                        <div class="flex flex-wrap gap-2">
                            <button type="button" @click="saveEdited()" class="inline-flex items-center px-3 py-2 bg-brand border border-transparent rounded-md font-bold text-sm text-white hover:bg-brand-dark">
                                {{ __('Spremi promjene') }}
                            </button>
                            <button type="button" @click="deleteSelected()" class="inline-flex items-center px-3 py-2 bg-red-600 border border-transparent rounded-md font-bold text-sm text-white hover:bg-red-500">
                                {{ __('Obriši zonu') }}
                            </button>
                            <button type="button" @click="deselect()" class="inline-flex items-center px-3 py-2 bg-white border border-line-strong rounded-md font-bold text-sm text-ink">
                                {{ __('Zatvori') }}
                            </button>
                        </div>
                    </div>
                </template>

                <div class="pt-4 border-t border-line">
                    <p class="text-xs font-semibold uppercase text-ink-faint mb-2">{{ __('Sve stavke') }}</p>
                    <ul class="space-y-1 text-sm">
                        <template x-for="zone in zones" :key="'list-'+zone.id">
                            <li
                                @mouseenter="hasShape(zone.points) && !selectedZoneId ? hoveredZoneId = zone.id : null"
                                @mouseleave="hoveredZoneId = null"
                                @click="hasShape(zone.points) && !drawing && !selectedZoneId ? selectZone(zone) : null"
                                class="flex items-center justify-between gap-2 rounded-md px-2 py-1 -mx-2 transition-colors"
                                :class="[hasShape(zone.points) ? 'cursor-pointer' : '', zone.id === hoveredZoneId ? 'bg-brand-light' : '']"
                            >
                                <span x-text="zone.label" class="text-ink"></span>
                                <span
                                    x-text="hasShape(zone.points) ? '{{ __('označeno') }}' : '{{ __('bez zone') }}'"
                                    :class="hasShape(zone.points) ? 'text-brand' : 'text-ink-faint'"
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
                    drawingClosed: false,
                    cursorPoint: null,
                    snapTarget: null,
                    attachToId: '',
                    newLabel: '',
                    selectedZoneId: null,
                    hoveredZoneId: null,
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

                        // While drawing/editing, other zones are only reference geometry:
                        // they must not capture the pointer (cursor + clicks), otherwise
                        // wall-to-wall neighbours make it impossible to place a point
                        // next to them.
                        const passive = this.drawing || !!this.selectedZoneId;

                        for (const zone of this.zones) {
                            if (!this.hasShape(zone.points) || zone.id === this.selectedZoneId) continue;

                            const hovered = !passive && zone.id === this.hoveredZoneId;
                            const c = this.toPx(this.centroid(zone.points));
                            html += `<g>`
                                + (passive
                                    ? `<polygon points="${this.toSvgPoints(zone.points)}" class="fill-brand/10 stroke-brand/70" stroke-width="1.5" style="pointer-events:none;"></polygon>`
                                    : `<polygon data-zone-id="${zone.id}" points="${this.toSvgPoints(zone.points)}" `
                                        + `class="${hovered ? 'fill-brand/45 stroke-brand' : 'fill-brand/25 stroke-brand'}" stroke-width="2" style="cursor:pointer;"></polygon>`)
                                + `<text x="${c[0]}" y="${c[1]}" text-anchor="middle" class="fill-white text-xs font-semibold pointer-events-none" `
                                + `style="paint-order: stroke; stroke: rgba(0,0,0,.6); stroke-width: 3px;">${this.escapeHtml(zone.label)}</text>`
                                + `</g>`;
                        }

                        // Snap targets: corners of the neighbouring zones.
                        if (passive) {
                            for (const zone of this.zones) {
                                if (!this.hasShape(zone.points) || zone.id === this.selectedZoneId) continue;
                                for (const p of zone.points) {
                                    const px = this.toPx(p);
                                    html += `<circle cx="${px[0]}" cy="${px[1]}" r="2.5" fill="white" stroke="#0F3F9E" stroke-width="1" style="pointer-events:none;"></circle>`;
                                }
                            }
                        }

                        if (passive && this.snapTarget) {
                            const s = this.toPx(this.snapTarget);
                            html += `<circle cx="${s[0]}" cy="${s[1]}" r="9" fill="none" stroke="#22c55e" stroke-width="2.5" style="pointer-events:none;"></circle>`;
                        }

                        if (this.selectedZoneId && !this.drawing) {
                            html += `<g><polygon points="${this.toSvgPoints(this.editPoints)}" class="fill-navy-900/20 stroke-navy-900" stroke-width="2"></polygon>`;
                            this.editPoints.forEach((p, index) => {
                                const px = this.toPx(p);
                                html += `<circle data-vertex-index="${index}" cx="${px[0]}" cy="${px[1]}" r="7" `
                                    + `fill="#0A1E45" stroke="white" stroke-width="1.5" style="cursor:grab;"></circle>`;
                            });
                            html += `</g>`;
                        }

                        if (this.drawing) {
                            const canClose = this.newPoints.length >= 3;

                            if (this.drawingClosed) {
                                html += `<polygon points="${this.toSvgPoints(this.newPoints)}" fill="rgba(245,158,11,.25)" stroke="#f59e0b" stroke-width="2"></polygon>`;
                            } else if (this.newPoints.length > 0) {
                                // Open polyline (+ a rubber-band segment to the cursor);
                                // the shape only closes when the user clicks the first point.
                                const open = this.cursorPoint ? [...this.newPoints, this.cursorPoint] : this.newPoints;
                                html += `<polyline points="${this.toSvgPoints(open)}" fill="none" stroke="#f59e0b" stroke-width="2" stroke-dasharray="4"></polyline>`;
                            }

                            this.newPoints.forEach((p, index) => {
                                const px = this.toPx(p);
                                const isStart = canClose && !this.drawingClosed && index === 0;
                                html += isStart
                                    ? `<circle cx="${px[0]}" cy="${px[1]}" r="9" fill="#f59e0b" stroke="white" stroke-width="2" style="cursor:pointer;"></circle>`
                                    : `<circle cx="${px[0]}" cy="${px[1]}" r="4" fill="#f59e0b"></circle>`;
                            });
                        }

                        return html;
                    },

                    // Snaps a point (in %) to the nearest corner of any other zone, or failing
                    // that to the nearest spot on another zone's edge, so wall-to-wall
                    // apartments can share exact boundaries. Tolerances are in screen px.
                    snapPoint(point, excludeZoneId = null, vertexTol = 12, edgeTol = 8) {
                        let bestVertex = null;
                        let bestVertexDist = vertexTol;
                        let bestEdge = null;
                        let bestEdgeDist = edgeTol;
                        const p = this.toPx(point);

                        for (const zone of this.zones) {
                            if (!this.hasShape(zone.points) || zone.id === excludeZoneId) continue;

                            for (let i = 0; i < zone.points.length; i++) {
                                const a = this.toPx(zone.points[i]);
                                const b = this.toPx(zone.points[(i + 1) % zone.points.length]);

                                const vd = Math.hypot(p[0] - a[0], p[1] - a[1]);
                                if (vd < bestVertexDist) {
                                    bestVertexDist = vd;
                                    bestVertex = zone.points[i];
                                }

                                const dx = b[0] - a[0], dy = b[1] - a[1];
                                const lenSq = dx * dx + dy * dy;
                                const t = lenSq === 0 ? 0 : Math.max(0, Math.min(1, ((p[0] - a[0]) * dx + (p[1] - a[1]) * dy) / lenSq));
                                const q = [a[0] + t * dx, a[1] + t * dy];
                                const ed = Math.hypot(p[0] - q[0], p[1] - q[1]);
                                if (ed < bestEdgeDist) {
                                    bestEdgeDist = ed;
                                    bestEdge = [
                                        Math.round((q[0] / this.imgW) * 10000) / 100,
                                        Math.round((q[1] / this.imgH) * 10000) / 100,
                                    ];
                                }
                            }
                        }

                        const target = bestVertex ? [...bestVertex] : bestEdge;
                        return target ? { point: target, snapped: true } : { point, snapped: false };
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
                        this.drawingClosed = false;
                        this.cursorPoint = null;
                        this.attachToId = '';
                        this.newLabel = '';
                        this.selectedZoneId = null;
                    },

                    cancelDrawing() {
                        this.drawing = false;
                        this.newPoints = [];
                        this.drawingClosed = false;
                        this.cursorPoint = null;
                    },

                    undoPoint() {
                        // Undoing on a closed shape re-opens it so drawing can continue.
                        this.drawingClosed = false;
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
                            if (this.drawingClosed) return;

                            const point = this.posFromEvent(e);

                            // The shape stays open until the user clicks back on the
                            // first point (needs >= 3 points) -- that is the only thing
                            // that closes it, so any number of points can be drawn.
                            if (this.newPoints.length >= 3 && this.isNearPoint(point, this.newPoints[0])) {
                                this.drawingClosed = true;
                                this.cursorPoint = null;
                                return;
                            }

                            const snap = e.shiftKey ? { point } : this.snapPoint(point);
                            this.newPoints.push(snap.point);
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

                    // Combines vertex-dragging (while editing a selected zone) with
                    // hover detection on the default browse view, since an element can
                    // only have one @mousemove handler.
                    onSvgMouseMove(e) {
                        if (this.draggingIndex !== null) {
                            this.onDrag(e);
                            return;
                        }

                        if (this.drawing) {
                            if (this.drawingClosed) {
                                this.cursorPoint = null;
                                this.snapTarget = null;
                                return;
                            }

                            const raw = this.posFromEvent(e);
                            const snap = e.shiftKey ? { point: raw, snapped: false } : this.snapPoint(raw);
                            this.snapTarget = snap.snapped ? snap.point : null;
                            this.cursorPoint = this.newPoints.length === 0 ? null : snap.point;
                            return;
                        }

                        if (this.selectedZoneId) return;

                        const zoneEl = e.target.closest('[data-zone-id]');
                        this.hoveredZoneId = zoneEl ? parseInt(zoneEl.dataset.zoneId) : null;
                    },

                    onSvgMouseDown(e) {
                        const vertexEl = e.target.closest('[data-vertex-index]');
                        if (vertexEl) {
                            this.draggingIndex = parseInt(vertexEl.dataset.vertexIndex);
                            this.didDrag = false;
                        }
                    },

                    async save() {
                        if (this.newPoints.length < 3 || !this.drawingClosed) return;
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
                        this.drawingClosed = false;
                        this.cursorPoint = null;
                    },

                    selectZone(zone) {
                        if (this.drawing) return;
                        if (!this.hasShape(zone.points)) return;
                        this.selectedZoneId = zone.id;
                        this.hoveredZoneId = null;
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
                        const raw = this.posFromEvent(e);
                        const snap = e.shiftKey ? { point: raw, snapped: false } : this.snapPoint(raw, this.selectedZoneId);
                        this.snapTarget = snap.snapped ? snap.point : null;
                        this.editPoints[this.draggingIndex] = snap.point;
                    },

                    stopDrag() {
                        this.draggingIndex = null;
                        this.snapTarget = null;
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
