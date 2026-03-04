{{-- Module 9999: Listing --}}
<div class="listing-module">
    @if($title)
        <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ $title }}</h2>
    @endif

    {{-- Featured Items --}}
    @if($featured->count() > 0)
        <div class="mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($featured as $feat)
                    <a href="/{{ $feat->slug }}" class="group relative block rounded-xl overflow-hidden shadow-md hover:shadow-lg transition-shadow">
                        @if($showImage && $feat->getFirstMediaUrl('cover'))
                            <img src="{{ $feat->getFirstMediaUrl('cover') }}" alt="{{ $feat->title }}"
                                 class="w-full h-64 object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="w-full h-64 bg-gradient-to-br from-indigo-500 to-purple-600"></div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-6">
                            <span class="inline-block px-3 py-1 bg-indigo-600 text-white text-xs font-medium rounded-full mb-2">Öne Çıkan</span>
                            <h3 class="text-xl font-bold text-white">{{ $feat->title }}</h3>
                            @if($showExcerpt && $feat->excerpt)
                                <p class="text-white/80 text-sm mt-1 line-clamp-2">{{ $feat->excerpt }}</p>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Subcategories --}}
    @if($subcategories->count() > 0)
        <div class="flex flex-wrap gap-2 mb-6">
            @foreach($subcategories as $sub)
                <a href="/{{ $sub->slug }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-indigo-50 hover:text-indigo-700 transition-colors">
                    {{ $sub->title }}
                    @if($sub->articles_count > 0)
                        <span class="ml-1.5 px-1.5 py-0.5 bg-gray-200 text-gray-500 text-xs rounded-full">{{ $sub->articles_count }}</span>
                    @endif
                </a>
            @endforeach
        </div>
    @endif

    {{-- Articles --}}
    @if($displayMode === 'list')
        {{-- List View --}}
        <div class="space-y-4">
            @forelse($articles as $art)
                <article class="flex gap-5 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                    @if($showImage && $art->getFirstMediaUrl('cover'))
                        <a href="/{{ $art->slug }}" class="flex-shrink-0 w-48 overflow-hidden">
                            <img src="{{ $art->getFirstMediaUrl('cover') }}" alt="{{ $art->title }}"
                                 class="w-full h-full object-cover hover:scale-105 transition-transform duration-300" loading="lazy">
                        </a>
                    @endif
                    <div class="flex-1 p-4">
                        <a href="/{{ $art->slug }}">
                            <h3 class="text-lg font-semibold text-gray-800 hover:text-indigo-600 transition-colors">{{ $art->title }}</h3>
                        </a>
                        @if($art->published_at)
                            <time class="text-xs text-gray-400 mt-1 block">{{ $art->published_at->format('d.m.Y') }}</time>
                        @endif
                        @if($showExcerpt && $art->excerpt)
                            <p class="text-sm text-gray-600 mt-2 line-clamp-2">{{ $art->excerpt }}</p>
                        @endif
                    </div>
                </article>
            @empty
                <div class="text-center py-12 text-gray-500">
                    <p>Henüz içerik eklenmemiş.</p>
                </div>
            @endforelse
        </div>
    @else
        {{-- Grid View --}}
        <div class="grid grid-cols-1 md:grid-cols-{{ $columns }} gap-6">
            @forelse($articles as $art)
                <article class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow group">
                    @if($showImage && $art->getFirstMediaUrl('cover'))
                        <a href="/{{ $art->slug }}" class="block overflow-hidden">
                            <img src="{{ $art->getFirstMediaUrl('cover') }}" alt="{{ $art->title }}"
                                 class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy">
                        </a>
                    @endif
                    <div class="p-4">
                        <a href="/{{ $art->slug }}">
                            <h3 class="font-semibold text-gray-800 group-hover:text-indigo-600 transition-colors">{{ $art->title }}</h3>
                        </a>
                        @if($art->published_at)
                            <time class="text-xs text-gray-400 mt-1 block">{{ $art->published_at->format('d.m.Y') }}</time>
                        @endif
                        @if($showExcerpt && $art->excerpt)
                            <p class="text-sm text-gray-500 mt-2 line-clamp-2">{{ $art->excerpt }}</p>
                        @endif
                    </div>
                </article>
            @empty
                <div class="col-span-full text-center py-12 text-gray-500">
                    <p>Henüz içerik eklenmemiş.</p>
                </div>
            @endforelse
        </div>
    @endif

    {{-- Pagination --}}
    @if($articles->hasPages())
        <div class="mt-8">{{ $articles->links() }}</div>
    @endif
</div>
