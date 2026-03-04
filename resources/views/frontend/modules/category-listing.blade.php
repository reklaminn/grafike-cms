{{-- Module 123: Category Listing --}}
<div class="category-listing">
    @if($title)
        <h3 class="text-xl font-semibold text-gray-800 mb-6">{{ $title }}</h3>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-{{ $columns }} gap-6">
        @foreach($categories as $category)
            <a href="/{{ $category->slug }}" class="group block bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                @if($category->getFirstMediaUrl('cover'))
                    <img src="{{ $category->getFirstMediaUrl('cover') }}" alt="{{ $category->title }}"
                         class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
                @endif
                <div class="p-4">
                    <h4 class="font-semibold text-gray-800 group-hover:text-indigo-600 transition-colors">{{ $category->title }}</h4>
                    @if($showCount)
                        <span class="text-xs text-gray-400 mt-1">{{ $category->articles_count }} içerik</span>
                    @endif
                </div>
            </a>
        @endforeach
    </div>
</div>
