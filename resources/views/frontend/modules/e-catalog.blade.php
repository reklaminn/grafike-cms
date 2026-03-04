{{-- Module 125: E-Catalog --}}
<div class="e-catalog">
    @if($title)
        <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ $title }}</h2>
    @endif

    @if($subcategories->count() > 0)
        <div class="flex flex-wrap gap-2 mb-6">
            @foreach($subcategories as $sub)
                <a href="/{{ $sub->slug }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-indigo-50 hover:text-indigo-700 transition-colors">
                    {{ $sub->title }}
                </a>
            @endforeach
        </div>
    @endif

    <div class="grid grid-cols-2 md:grid-cols-{{ $columns }} gap-6">
        @foreach($products as $product)
            <a href="/{{ $product->slug }}" class="group block bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-all">
                @if($product->getFirstMediaUrl('cover'))
                    <div class="overflow-hidden">
                        <img src="{{ $product->getFirstMediaUrl('cover') }}" alt="{{ $product->title }}"
                             class="w-full h-56 object-cover group-hover:scale-105 transition-transform duration-300">
                    </div>
                @endif
                <div class="p-4">
                    <h4 class="font-semibold text-gray-800 group-hover:text-indigo-600 transition-colors">{{ $product->title }}</h4>
                    @if($product->excerpt)
                        <p class="text-sm text-gray-500 mt-1 line-clamp-2">{{ $product->excerpt }}</p>
                    @endif
                </div>
            </a>
        @endforeach
    </div>

    @if($products->hasPages())
        <div class="mt-8">{{ $products->links() }}</div>
    @endif
</div>
