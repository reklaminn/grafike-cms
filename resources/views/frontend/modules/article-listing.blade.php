{{-- Module 130: Article Listing --}}
<div class="article-listing">
    @if($title)
        <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ $title }}</h2>
    @endif

    @if($columns === 1)
        {{-- List view --}}
        <div class="space-y-6">
            @foreach($articles as $art)
                <article class="flex gap-6 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                    @if($showImage && $art->getFirstMediaUrl('cover'))
                        <a href="/{{ $art->slug }}" class="flex-shrink-0 w-64 overflow-hidden">
                            <img src="{{ $art->getFirstMediaUrl('cover') }}" alt="{{ $art->title }}"
                                 class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                        </a>
                    @endif
                    <div class="flex-1 p-5">
                        <a href="/{{ $art->slug }}">
                            <h3 class="text-lg font-semibold text-gray-800 hover:text-indigo-600 transition-colors">{{ $art->title }}</h3>
                        </a>
                        @if($showDate && $art->published_at)
                            <time class="text-xs text-gray-400 mt-1 block">{{ $art->published_at->format('d.m.Y') }}</time>
                        @endif
                        @if($showExcerpt && $art->excerpt)
                            <p class="text-sm text-gray-600 mt-2 line-clamp-3">{{ $art->excerpt }}</p>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    @else
        {{-- Grid view --}}
        <div class="grid grid-cols-1 md:grid-cols-{{ $columns }} gap-6">
            @foreach($articles as $art)
                <article class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                    @if($showImage && $art->getFirstMediaUrl('cover'))
                        <a href="/{{ $art->slug }}" class="block overflow-hidden">
                            <img src="{{ $art->getFirstMediaUrl('cover') }}" alt="{{ $art->title }}"
                                 class="w-full h-48 object-cover hover:scale-105 transition-transform duration-300">
                        </a>
                    @endif
                    <div class="p-4">
                        <a href="/{{ $art->slug }}">
                            <h3 class="font-semibold text-gray-800 hover:text-indigo-600 transition-colors">{{ $art->title }}</h3>
                        </a>
                        @if($showDate && $art->published_at)
                            <time class="text-xs text-gray-400 mt-1 block">{{ $art->published_at->format('d.m.Y') }}</time>
                        @endif
                        @if($showExcerpt && $art->excerpt)
                            <p class="text-sm text-gray-500 mt-2 line-clamp-2">{{ $art->excerpt }}</p>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    @endif

    @if($articles->hasPages())
        <div class="mt-8">{{ $articles->links() }}</div>
    @endif
</div>
