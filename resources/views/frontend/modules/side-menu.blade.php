{{-- Module 87: Side Menu --}}
<nav class="side-menu">
    @if($title)
        <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">{{ $title }}</h3>
    @endif
    <ul class="space-y-1">
        @foreach($menuItems as $item)
            <li>
                <a href="/{{ $item->slug }}"
                   class="block px-3 py-2 text-sm rounded-lg transition-colors {{ $item->id === $currentPageId ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    {{ $item->title }}
                </a>
                @php $children = \App\Models\Page::where('parent_id', $item->id)->where('status', 'published')->where('show_in_menu', true)->orderBy('sort_order')->get(); @endphp
                @if($children->count() > 0)
                    <ul class="ml-4 mt-1 space-y-1">
                        @foreach($children as $child)
                            <li>
                                <a href="/{{ $child->slug }}"
                                   class="block px-3 py-1.5 text-xs rounded {{ $child->id === $currentPageId ? 'text-indigo-600 font-medium' : 'text-gray-500 hover:text-gray-700' }}">
                                    {{ $child->title }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </li>
        @endforeach
    </ul>
</nav>
