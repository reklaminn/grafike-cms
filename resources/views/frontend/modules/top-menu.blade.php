{{-- Module 137: Top Menu --}}
<nav class="top-menu" x-data="{ openDropdown: null }">
    <ul class="hidden md:flex items-center space-x-1">
        @foreach($menuItems as $item)
            <li class="relative"
                @if($item->children->count() > 0)
                    @mouseenter="openDropdown = {{ $item->id }}"
                    @mouseleave="openDropdown = null"
                @endif>
                <a href="{{ $item->url ?: ($item->page ? '/' . $item->page->slug : '#') }}"
                   @if($item->target) target="{{ $item->target }}" @endif
                   class="px-4 py-2 text-sm font-medium rounded-md transition-colors
                          {{ $item->page?->slug === $currentSlug ? 'text-indigo-600 bg-indigo-50' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }}">
                    {{ $item->title }}
                    @if($item->children->count() > 0)
                        <svg class="w-4 h-4 inline-block ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    @endif
                </a>

                {{-- Dropdown --}}
                @if($item->children->count() > 0)
                    <div x-show="openDropdown === {{ $item->id }}"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 translate-y-1"
                         x-cloak
                         class="absolute left-0 top-full mt-1 w-56 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50">
                        @foreach($item->children as $child)
                            <a href="{{ $child->url ?: ($child->page ? '/' . $child->page->slug : '#') }}"
                               @if($child->target) target="{{ $child->target }}" @endif
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors">
                                {{ $child->title }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </li>
        @endforeach
    </ul>

    {{-- Mobile Menu --}}
    <div class="md:hidden" x-show="typeof mobileMenuOpen !== 'undefined' && mobileMenuOpen" x-cloak>
        <ul class="space-y-1 py-3">
            @foreach($menuItems as $item)
                <li x-data="{ subOpen: false }">
                    <div class="flex items-center">
                        <a href="{{ $item->url ?: ($item->page ? '/' . $item->page->slug : '#') }}"
                           class="flex-1 px-4 py-2 text-sm font-medium rounded-md
                                  {{ $item->page?->slug === $currentSlug ? 'text-indigo-600 bg-indigo-50' : 'text-gray-700 hover:bg-gray-50' }}">
                            {{ $item->title }}
                        </a>
                        @if($item->children->count() > 0)
                            <button @click="subOpen = !subOpen" class="px-3 py-2 text-gray-500">
                                <svg :class="subOpen ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                        @endif
                    </div>
                    @if($item->children->count() > 0)
                        <ul x-show="subOpen" x-collapse class="pl-6 space-y-1">
                            @foreach($item->children as $child)
                                <li>
                                    <a href="{{ $child->url ?: ($child->page ? '/' . $child->page->slug : '#') }}"
                                       class="block px-4 py-2 text-sm text-gray-600 hover:text-indigo-600 rounded-md hover:bg-gray-50">
                                        {{ $child->title }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
</nav>
