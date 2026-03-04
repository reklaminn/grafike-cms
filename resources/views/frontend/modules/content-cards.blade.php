{{-- Module 135: Content Cards --}}
<div class="content-cards">
    @if($title)
        <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ $title }}</h2>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-{{ $columns }} gap-6">
        @foreach($articles as $art)
            <article class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow group">
                @if($art->getFirstMediaUrl('cover'))
                    <a href="/{{ $art->slug }}" class="block overflow-hidden">
                        <img src="{{ $art->getFirstMediaUrl('cover') }}" alt="{{ $art->title }}"
                             class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300"
                             loading="lazy">
                    </a>
                @endif
                <div class="p-5">
                    <a href="/{{ $art->slug }}">
                        <h3 class="font-semibold text-gray-800 group-hover:text-indigo-600 transition-colors text-lg">
                            {{ $art->title }}
                        </h3>
                    </a>
                    @if($showExcerpt && $art->excerpt)
                        <p class="text-sm text-gray-500 mt-2 line-clamp-3">{{ $art->excerpt }}</p>
                    @endif
                    <a href="/{{ $art->slug }}" class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-700 mt-3 font-medium">
                        Devamını Oku
                        <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </article>
        @endforeach
    </div>
</div>
