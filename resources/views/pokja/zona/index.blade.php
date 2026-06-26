<x-app-layout>
    <x-slot name="header">Manajemen Zona Wilayah</x-slot>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.css" />

    <style>
        #zona-map { height: 60vh; min-height: 450px; border-radius: 1rem; z-index: 1; }
    </style>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Sidebar: Zona List --}}
        <div class="lg:col-span-1 space-y-4">
            <div class="glass-card p-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-4 gap-2">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                        <i data-lucide="layers" class="w-4 h-4 text-purple-400"></i> Daftar Zona
                    </h3>
                    <form method="GET" action="{{ route('pokja.zona.index') }}" class="flex items-center gap-2">
                        <label for="per_page" class="text-xs font-bold text-slate-500 uppercase">Baris:</label>
                        <select name="per_page" id="per_page" onchange="this.form.submit()" class="px-2 py-1 text-xs border border-slate-200/50 dark:border-slate-700/50 bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                            <option value="15" {{ $perPage == 15 ? 'selected' : '' }}>15</option>
                            <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </form>
                </div>
                <div id="zona-list" class="space-y-3 max-h-[40vh] overflow-y-auto pr-1">
                    @forelse($zonas as $zona)
                        <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200/50 dark:border-slate-700/50 zona-item" data-id="{{ $zona->id }}">
                            <div class="flex items-center gap-3">
                                <span class="w-4 h-4 rounded-full flex-shrink-0" style="background:{{ $zona->warna }};border:2px solid {{ $zona->warna_border }}"></span>
                                <div>
                                    <p class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ $zona->nama }}</p>
                                    <p class="text-[10px] text-slate-500">{{ $zona->dudis_count }} DUDI</p>
                                </div>
                            </div>
                            @if(auth()->user()->role !== 'kepala_sekolah')
                            <button type="button" onclick="deleteZona({{ $zona->id }})" class="p-1.5 text-red-400 hover:bg-red-500/10 rounded-lg transition-colors">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                            @endif
                        </div>
                    @empty
                        <p class="text-sm text-slate-500 italic text-center py-4">Belum ada zona. Gambar polygon di peta untuk menambah.</p>
                    @endforelse
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800">
                    {{ $zonas->links() }}
                </div>
            </div>

            @if(auth()->user()->role !== 'kepala_sekolah')
            {{-- Form Add Zona --}}
            <div class="glass-card p-6" id="zona-form-card">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100 mb-4 flex items-center gap-2">
                    <i data-lucide="plus-circle" class="w-4 h-4 text-blue-400"></i> Tambah Zona Baru
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">Gambar polygon di peta menggunakan tool gambar, lalu isi form di bawah ini.</p>
                <form id="zona-form" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nama Zona</label>
                        <input type="text" id="zona-nama" required placeholder="Contoh: Zona 1 - Ciamis Utara"
                               class="w-full px-3 py-2 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 text-sm text-slate-800 dark:text-slate-200">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Warna Isi</label>
                            <input type="color" id="zona-warna" value="#3b82f6" class="w-full h-10 rounded-xl cursor-pointer border border-slate-200/50 dark:border-slate-700/50">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Warna Garis</label>
                            <input type="color" id="zona-warna-border" value="#1e40af" class="w-full h-10 rounded-xl cursor-pointer border border-slate-200/50 dark:border-slate-700/50">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nomor Zona</label>
                        <input type="number" id="zona-nomor" placeholder="1" min="1"
                               class="w-full px-3 py-2 bg-slate-100 dark:bg-slate-900/50 border border-slate-200/50 dark:border-slate-700/50 rounded-xl focus:ring-2 focus:ring-blue-500 text-sm text-slate-800 dark:text-slate-200">
                    </div>
                    <input type="hidden" id="zona-koordinat" value="">
                    <button type="submit" id="btn-simpan-zona" disabled
                            class="w-full px-4 py-2.5 bg-blue-600 hover:bg-blue-500 disabled:bg-slate-400 disabled:cursor-not-allowed text-white text-xs font-bold rounded-xl transition-all shadow-lg shadow-blue-600/20 flex items-center justify-center gap-2">
                        <i data-lucide="save" class="w-4 h-4"></i> Simpan Zona
                    </button>
                </form>
            </div>
            @endif
        </div>

        {{-- Map --}}
        <div class="lg:col-span-2">
            <div class="glass-card p-2 overflow-hidden">
                <div id="zona-map"></div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const map = L.map('zona-map').setView([-7.3305, 108.3521], 11);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        // Existing zones
        const existingZonas = @json($zonas);
        existingZonas.forEach(zona => {
            if (!zona.koordinat_geojson || zona.koordinat_geojson.length < 3) return;
            const latlngs = zona.koordinat_geojson.map(c => [c[1], c[0]]);
            L.polygon(latlngs, {
                color: zona.warna_border,
                weight: 2,
                fillColor: zona.warna,
                fillOpacity: 0.2,
                dashArray: '5, 5'
            }).addTo(map).bindTooltip(zona.nama, { permanent: true, direction: 'center', className: 'zona-label' });
        });

        @if(auth()->user()->role !== 'kepala_sekolah')
        // Draw controls
        const drawnItems = new L.FeatureGroup();
        map.addLayer(drawnItems);

        const drawControl = new L.Control.Draw({
            draw: {
                polygon: {
                    allowIntersection: false,
                    showArea: true,
                    shapeOptions: { color: '#3b82f6', weight: 2 }
                },
                polyline: false,
                circle: false,
                rectangle: false,
                marker: false,
                circlemarker: false
            },
            edit: {
                featureGroup: drawnItems,
                remove: true
            }
        });
        map.addControl(drawControl);
        @endif

        let currentCoords = null;

        map.on(L.Draw.Event.CREATED, function(e) {
            drawnItems.clearLayers();
            drawnItems.addLayer(e.layer);

            // Extract coordinates as [lng, lat] (GeoJSON format)
            const latlngs = e.layer.getLatLngs()[0];
            currentCoords = latlngs.map(ll => [ll.lng, ll.lat]);

            document.getElementById('zona-koordinat').value = JSON.stringify(currentCoords);
            document.getElementById('btn-simpan-zona').disabled = false;
        });

        map.on(L.Draw.Event.DELETED, function() {
            currentCoords = null;
            document.getElementById('zona-koordinat').value = '';
            document.getElementById('btn-simpan-zona').disabled = true;
        });

        // Form submit
        document.getElementById('zona-form').addEventListener('submit', function(e) {
            e.preventDefault();

            const nama = document.getElementById('zona-nama').value;
            const warna = document.getElementById('zona-warna').value;
            const warnaBorder = document.getElementById('zona-warna-border').value;
            const nomor = document.getElementById('zona-nomor').value;
            const koordinat = document.getElementById('zona-koordinat').value;

            if (!koordinat) {
                alert('Gambar polygon di peta terlebih dahulu!');
                return;
            }

            fetch('{{ route("pokja.zona.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    nama: nama,
                    warna: warna,
                    warna_border: warnaBorder,
                    koordinat_geojson: koordinat,
                    nomor_zona: nomor || null
                })
            })
            .then(r => r.json())
            .then(data => {
                alert(data.message);
                window.location.reload();
            })
            .catch(err => alert('Gagal menyimpan: ' + err.message));
        });

        // Delete zona
        window.deleteZona = function(id) {
            if (!confirm('Hapus zona ini? DUDI dalam zona ini akan menjadi tanpa zona.')) return;

            fetch(`{{ url('pokja/zona') }}/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(r => r.json())
            .then(data => {
                alert(data.message);
                window.location.reload();
            })
            .catch(err => alert('Gagal menghapus: ' + err.message));
        };
    });
    </script>
</x-app-layout>
