{{-- Module 121: Filter Menu --}}
<div class="filter-menu" x-data="{ activeFilter: '{{ $currentFilter ?? 'all' }}' }">
    @if($title)
        <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ $title }}</h3>
    @endif

    <div class="flex flex-wrap gap-2 mb-6">
        <a href="?filter=all"
           @click="activeFilter = 'all'"
           :class="activeFilter === 'all' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
           class="px-4 py-2 rounded-full text-sm font-medium transition-colors">
            Tümü
        </a>
        @foreach($filterCategories as $cat)
            <a href="?filter={{ $cat->slug }}"
               @click="activeFilter = '{{ $cat->slug }}'"
               :class="activeFilter === '{{ $cat->slug }}' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
               class="px-4 py-2 rounded-full text-sm font-medium transition-colors">
                {{ $cat->title }}
            </a>
        @endforeach
    </div>
</div>
