{{-- Module 131: Category Cards --}}
<div class="category-cards">
    @if($title)
        <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ $title }}</h2>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-{{ $columns }} gap-6">
        @foreach($categories as $category)
            <a href="/{{ $category->slug }}" class="group relative block rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300">
                {{-- Card Background Image --}}
                @if($category->getFirstMediaUrl('cover'))
                    <div class="aspect-[4/3] overflow-hidden">
                        <img src="{{ $category->getFirstMediaUrl('cover') }}" alt="{{ $category->title }}"
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    </div>
                @else
                    <div class="aspect-[4/3] bg-gradient-to-br from-indigo-500 to-purple-600"></div>
                @endif

                {{-- Overlay --}}
                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent group-hover:from-black/80 transition-colors"></div>

                {{-- Content --}}
                <div class="absolute bottom-0 left-0 right-0 p-5">
                    <h3 class="text-lg font-bold text-white mb-1">{{ $category->title }}</h3>
                    @if($category->articles_count > 0)
                        <span class="text-sm text-white/80">{{ $category->articles_count }} içerik</span>
                    @endif
                </div>
            </a>
        @endforeach
    </div>
</div>
