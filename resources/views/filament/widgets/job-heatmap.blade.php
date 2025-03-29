<x-filament::widget>
    <x-slot name="header">
        NHS Job Heatmap
    </x-slot>

    <div wire:ignore>
        <div id="heatmap" style="height: 500px; z-index: 0;"></div>
    </div>

    {{-- Leaflet CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    {{-- Leaflet JS --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.heat/dist/leaflet-heat.js"></script>

    {{-- 💡 Place your JS here --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const el = document.getElementById('heatmap');
            if (!el || el.dataset.mapLoaded) return;

            const map = L.map(el).setView([52.3555, -1.1743], 6);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            L.heatLayer([
                [51.505, -0.09, 1],
                [52.4862, -1.8904, 1],
                [53.4839, -2.2446, 1],
            ], {
                radius: 25,
                blur: 15
            }).addTo(map);

            el.dataset.mapLoaded = true;

            // ✅ Critical: ensure map tiles render inside Filament layout
            map.whenReady(() => {
                setTimeout(() => {
                    map.invalidateSize();
                }, 300);
            });
        });
    </script>
</x-filament::widget>