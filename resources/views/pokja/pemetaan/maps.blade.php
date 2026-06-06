<x-app-layout>
    <x-slot name="header">Peta Sebaran DUDI</x-slot>

    {{-- Leaflet CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />

    <style>
        #peta-dudi { height: 70vh; min-height: 500px; border-radius: 1rem; z-index: 1; }
        .marker-icon { display: flex; align-items: center; justify-content: center; border-radius: 50%; width: 32px; height: 32px; border: 3px solid #fff; box-shadow: 0 2px 8px rgba(0,0,0,.3); font-size: 13px; font-weight: 700; color: #fff; }
        .leaflet-popup-content-wrapper { border-radius: .75rem !important; }
        .leaflet-popup-content { margin: 12px 16px !important; font-size: 13px; }
        .leaflet-tooltip { border-radius: .5rem !important; font-size: 12px; padding: 6px 10px !important; }
    </style>

    {{-- Stats Bar --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="glass-card p-4 border-l-4 border-blue-500">
            <p class="text-[10px] text-blue-400 font-black uppercase tracking-widest leading-none mb-1">Total DUDI</p>
            <p class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ $totalDudi }}</p>
        </div>
        <div class="glass-card p-4 border-l-4 border-emerald-500">
            <p class="text-[10px] text-emerald-400 font-black uppercase tracking-widest leading-none mb-1">Terplot di Peta</p>
            <p class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ $dudiWithCoords }}</p>
        </div>
        <div class="glass-card p-4 border-l-4 border-purple-500">
            <p class="text-[10px] text-purple-400 font-black uppercase tracking-widest leading-none mb-1">Total Zona</p>
            <p class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ $totalZona }}</p>
        </div>
        <div class="glass-card p-4 border-l-4 border-amber-500">
            <p class="text-[10px] text-amber-400 font-black uppercase tracking-widest leading-none mb-1">Siswa di DUDI</p>
            <p class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ $totalSiswa }}</p>
        </div>
    </div>

    {{-- Legend --}}
    <div class="glass-card p-4 mb-6">
        <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-widest mb-3 flex items-center gap-2">
            <i data-lucide="palette" class="w-4 h-4 text-blue-400"></i> Legenda Jenis Industri
        </h4>
        <div class="flex flex-wrap gap-3" id="legend-container">
            {{-- Dynamically populated --}}
        </div>
    </div>

    {{-- Map --}}
    <div class="glass-card p-2 overflow-hidden">
        <div id="peta-dudi"></div>
    </div>

    {{-- Leaflet JS --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Color mapping for jenis_industri
        const colorMap = {
            'pemerintahan': '#ef4444',
            'industri': '#3b82f6',
            'layanan': '#8b5cf6',
            'perdagangan': '#f59e0b',
            'pendidikan': '#10b981',
            'kesehatan': '#ec4899',
            'teknologi': '#06b6d4',
            'pertanian': '#84cc16',
            'lainnya': '#6b7280'
        };

        const labelMap = {
            'pemerintahan': 'Pemerintahan',
            'industri': 'Industri',
            'layanan': 'Layanan/Jasa',
            'perdagangan': 'Perdagangan',
            'pendidikan': 'Pendidikan',
            'kesehatan': 'Kesehatan',
            'teknologi': 'Teknologi',
            'pertanian': 'Pertanian',
            'lainnya': 'Lainnya'
        };

        // Build legend
        const legendContainer = document.getElementById('legend-container');
        for (const [key, color] of Object.entries(colorMap)) {
            const item = document.createElement('div');
            item.className = 'flex items-center gap-2 px-3 py-1.5 bg-slate-100 dark:bg-slate-800/50 rounded-lg border border-slate-200/50 dark:border-slate-700/50';
            item.innerHTML = `<span class="w-3 h-3 rounded-full flex-shrink-0" style="background:${color}"></span><span class="text-xs font-medium text-slate-700 dark:text-slate-300">${labelMap[key]}</span>`;
            legendContainer.appendChild(item);
        }

        // Init map — center on Ciamis, West Java
        const map = L.map('peta-dudi').setView([-7.3305, 108.3521], 11);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        const clusterGroup = L.markerClusterGroup({
            maxClusterRadius: 50,
            spiderfyOnMaxZoom: true,
            showCoverageOnHover: false,
            iconCreateFunction: function(cluster) {
                const count = cluster.getChildCount();
                let size = 'small';
                if (count > 10) size = 'medium';
                if (count > 30) size = 'large';
                return L.divIcon({
                    html: `<div class="marker-icon" style="background:#3b82f6;width:${size==='large'?44:size==='medium'?38:32}px;height:${size==='large'?44:size==='medium'?38:32}px;font-size:${size==='large'?15:13}px">${count}</div>`,
                    className: '',
                    iconSize: L.point(size==='large'?44:size==='medium'?38:32, size==='large'?44:size==='medium'?38:32)
                });
            }
        });

        // Fetch data
        const dataUrl = @json(route('shared.pemetaan.maps.data', [], true));
        fetch(dataUrl)
            .then(r => r.json())
            .then(data => {
                // Render zone polygons
                if (data.zonas && data.zonas.length > 0) {
                    data.zonas.forEach(zona => {
                        if (!zona.koordinat || zona.koordinat.length < 3) return;
                        const latlngs = zona.koordinat.map(c => [c[1], c[0]]);
                        L.polygon(latlngs, {
                            color: zona.warna_border,
                            weight: 2,
                            fillColor: zona.warna,
                            fillOpacity: 0.15,
                            dashArray: '5, 5'
                        }).addTo(map).bindTooltip(zona.nama, {
                            permanent: false,
                            direction: 'center',
                            className: 'zona-label'
                        });
                    });
                }

                // Render DUDI markers
                if (data.markers && data.markers.length > 0) {
                    const escapeHTML = (str) => {
                        if (!str) return '';
                        return String(str).replace(/[&<>'"]/g, 
                            tag => ({
                                '&': '&amp;',
                                '<': '&lt;',
                                '>': '&gt;',
                                "'": '&#39;',
                                '"': '&quot;'
                            }[tag])
                        );
                    };

                    data.markers.forEach(d => {
                        const color = colorMap[d.jenis_industri] || colorMap['lainnya'];
                        const icon = L.divIcon({
                            html: `<div class="marker-icon" style="background:${color}"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"/><path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"/><path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"/><path d="M10 6h4"/><path d="M10 10h4"/><path d="M10 14h4"/><path d="M10 18h4"/></svg></div>`,
                            className: '',
                            iconSize: [32, 32],
                            iconAnchor: [16, 16],
                        });

                        // Build hover tooltip content (jurusan summary)
                        let tooltipHtml = `<strong>${escapeHTML(d.nama)}</strong>`;
                        if (d.total_siswa > 0) {
                            tooltipHtml += `<br><span style="color:#6b7280;font-size:11px">${d.total_siswa} Siswa</span>`;
                            const jurusanEntries = Object.entries(d.jurusan);
                            if (jurusanEntries.length > 0) {
                                tooltipHtml += '<br>';
                                jurusanEntries.forEach(([nama, count]) => {
                                    tooltipHtml += `<span style="font-size:11px">• ${escapeHTML(nama)}: <strong>${count}</strong></span><br>`;
                                });
                            }
                        } else {
                            tooltipHtml += `<br><span style="color:#9ca3af;font-size:11px">Belum ada siswa</span>`;
                        }

                        // Build popup content (full detail on click)
                        let popupHtml = `
                            <div style="min-width:220px">
                                <h3 style="font-weight:700;font-size:14px;margin:0 0 4px 0">${escapeHTML(d.nama)}</h3>
                                <p style="color:#6b7280;font-size:12px;margin:0 0 8px 0">${escapeHTML(d.alamat) || '-'}${d.kota ? ', '+escapeHTML(d.kota) : ''}</p>
                                <div style="border-top:1px solid #e2e8f0;padding-top:8px;margin-top:8px">
                                    <p style="font-size:11px;color:#6b7280;margin:0"><strong>Jenis:</strong> ${escapeHTML(labelMap[d.jenis_industri]) || '-'}</p>
                                    ${d.nama_pimpinan ? `<p style="font-size:11px;color:#6b7280;margin:2px 0"><strong>Pimpinan:</strong> ${escapeHTML(d.nama_pimpinan)}</p>` : ''}
                                    ${d.no_telepon ? `<p style="font-size:11px;color:#6b7280;margin:2px 0"><strong>Telepon:</strong> ${escapeHTML(d.no_telepon)}</p>` : ''}
                                    ${d.zona ? `<p style="font-size:11px;color:#3b82f6;margin:2px 0"><strong>Zona:</strong> ${escapeHTML(d.zona)}</p>` : ''}
                                </div>`;

                        if (d.siswa_list && d.siswa_list.length > 0) {
                            popupHtml += `<div style="border-top:1px solid #e2e8f0;padding-top:8px;margin-top:8px"><p style="font-size:11px;font-weight:700;margin:0 0 4px 0">Siswa PKL (${d.total_siswa}):</p>`;
                            d.siswa_list.forEach(s => {
                                popupHtml += `<p style="font-size:11px;margin:1px 0;color:#374151">• ${escapeHTML(s.nama)} <span style="color:#9ca3af">(${escapeHTML(s.jurusan)})</span></p>`;
                            });
                            popupHtml += '</div>';
                        }
                        popupHtml += '</div>';

                        const marker = L.marker([d.lat, d.lng], { icon })
                            .bindTooltip(tooltipHtml, { direction: 'top', offset: [0, -16] })
                            .bindPopup(popupHtml, { maxWidth: 320 });

                        clusterGroup.addLayer(marker);
                    });
                }

                map.addLayer(clusterGroup);

                // Fit bounds if markers exist
                if (data.markers && data.markers.length > 0) {
                    const bounds = data.markers.map(d => [d.lat, d.lng]);
                    if (bounds.length > 0) {
                        map.fitBounds(bounds, { padding: [30, 30], maxZoom: 14 });
                    }
                }
            })
            .catch(err => console.error('Failed to load map data:', err));
    });
    </script>
</x-app-layout>
