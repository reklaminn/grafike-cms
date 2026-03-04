{{-- Module 136: Logo Menu --}}
<div class="logo-menu flex items-center justify-between">
    {{-- Logo --}}
    <a href="{{ $homeUrl }}" class="flex items-center flex-shrink-0">
        @if($logoUrl)
            <img src="{{ $logoUrl }}" alt="{{ $siteName }}" class="h-12 w-auto" @if($logoCss) style="{{ $logoCss }}" @endif>
        @else
            <span class="text-2xl font-bold text-gray-900">{{ $siteName }}</span>
        @endif
    </a>

    {{-- Mobile Toggle --}}
    @if($showMobileToggle)
        <button @click="mobileMenuOpen = !mobileMenuOpen"
                class="md:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-colors"
                aria-label="Menüyü aç/kapat">
            <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
            <svg x-show="mobileMenuOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    @endif
</div>
