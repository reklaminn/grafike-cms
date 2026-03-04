{{-- Module 134: Image Gallery --}}
<div class="image-gallery" x-data="{ lightboxOpen: false, currentImage: 0, images: [] }" x-init="images = {{ Js::from($images->map(fn($img) => ['url' => $img->getUrl(), 'alt' => $img->name])) }}">
    @if($title)
        <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ $title }}</h2>
    @endif

    {{-- Gallery Grid --}}
    <div class="grid grid-cols-2 md:grid-cols-{{ $columns }} gap-3">
        @foreach($images as $index => $image)
            <div class="group relative aspect-square overflow-hidden rounded-lg cursor-pointer bg-gray-100"
                 @if($lightbox) @click="currentImage = {{ $index }}; lightboxOpen = true" @endif>
                <img src="{{ $image->getUrl() }}" alt="{{ $image->name }}"
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                     loading="lazy">
                @if($lightbox)
                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors flex items-center justify-center">
                        <svg class="w-8 h-8 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                        </svg>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    {{-- Lightbox Modal --}}
    @if($lightbox)
        <div x-show="lightboxOpen" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/90"
             @keydown.escape.window="lightboxOpen = false"
             @keydown.arrow-left.window="currentImage = (currentImage - 1 + images.length) % images.length"
             @keydown.arrow-right.window="currentImage = (currentImage + 1) % images.length">

            {{-- Close Button --}}
            <button @click="lightboxOpen = false" class="absolute top-4 right-4 text-white/80 hover:text-white z-10">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            {{-- Prev Button --}}
            <button @click="currentImage = (currentImage - 1 + images.length) % images.length"
                    class="absolute left-4 text-white/80 hover:text-white z-10">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>

            {{-- Image --}}
            <img :src="images[currentImage]?.url" :alt="images[currentImage]?.alt"
                 class="max-w-[90vw] max-h-[90vh] object-contain">

            {{-- Next Button --}}
            <button @click="currentImage = (currentImage + 1) % images.length"
                    class="absolute right-4 text-white/80 hover:text-white z-10">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>

            {{-- Counter --}}
            <div class="absolute bottom-4 left-1/2 -translate-x-1/2 text-white/80 text-sm">
                <span x-text="currentImage + 1"></span> / <span x-text="images.length"></span>
            </div>
        </div>
    @endif
</div>
