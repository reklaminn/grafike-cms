{{-- Module 114: Image Listing --}}
<div class="image-listing">
    @if($title)
        <h3 class="text-xl font-semibold text-gray-800 mb-6">{{ $title }}</h3>
    @endif

    <div class="grid grid-cols-2 md:grid-cols-{{ $columns }} gap-4">
        @foreach($images as $image)
            <a href="{{ $image['url'] }}" class="group block relative overflow-hidden rounded-lg" data-lightbox="images">
                <img src="{{ $image['thumb'] }}" alt="{{ $image['alt'] }}"
                     class="w-full h-48 object-cover transition-transform duration-300 group-hover:scale-110">
                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all flex items-end">
                    <span class="text-white text-sm p-3 opacity-0 group-hover:opacity-100 transition-opacity">
                        {{ $image['title'] }}
                    </span>
                </div>
            </a>
        @endforeach
    </div>

    @if($articles->hasPages())
        <div class="mt-6">{{ $articles->links() }}</div>
    @endif
</div>
