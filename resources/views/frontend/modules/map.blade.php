{{-- Module 112: Google Map --}}
<div class="map-module">
    @if($title)
        <h3 class="text-xl font-semibold text-gray-800 mb-4">{{ $title }}</h3>
    @endif

    <div id="map-{{ uniqid() }}" style="height: {{ $height }}; width: 100%;" class="rounded-lg overflow-hidden shadow-sm border border-gray-200">
        @if(empty($apiKey))
            <div class="flex items-center justify-center h-full bg-gray-100 text-gray-500">
                <div class="text-center">
                    <i class="fas fa-map-marked-alt text-4xl mb-2"></i>
                    <p class="text-sm">Google Maps API anahtarı yapılandırılmamış.</p>
                </div>
            </div>
        @endif
    </div>

    @if(!empty($apiKey))
    @push('scripts')
    <script>
    (function() {
        const mapEl = document.querySelector('[id^="map-"]');
        if (!mapEl || !window.google) return;
        const map = new google.maps.Map(mapEl, {
            center: { lat: {{ $defaultLat }}, lng: {{ $defaultLng }} },
            zoom: {{ $zoom }}
        });
        @foreach($markers as $marker)
        new google.maps.Marker({
            position: { lat: {{ $marker['lat'] }}, lng: {{ $marker['lng'] }} },
            map: map,
            title: @json($marker['title'])
        });
        @endforeach
    })();
    </script>
    @endpush
    @endif
</div>
