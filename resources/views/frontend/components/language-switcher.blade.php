{{-- Language Switcher Component --}}
@if(isset($languages) && $languages->count() > 1)
    <div class="language-switcher" x-data="{ open: false }">
        <div class="relative">
            <button @click="open = !open" @click.away="open = false"
                    class="flex items-center gap-1.5 px-3 py-1.5 text-sm text-gray-600 hover:text-gray-900 rounded-md hover:bg-gray-50 transition-colors">
                <span class="uppercase font-medium">{{ $currentLanguage->code ?? app()->getLocale() }}</span>
                <svg class="w-3.5 h-3.5 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div x-show="open" x-cloak
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="absolute right-0 mt-1 w-36 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                @foreach($languages as $lang)
                    <a href="{{ $lang->url ?? '/' . $lang->code }}"
                       class="flex items-center gap-2 px-4 py-2 text-sm transition-colors
                              {{ ($currentLanguage->code ?? app()->getLocale()) === $lang->code
                                  ? 'bg-indigo-50 text-indigo-700 font-medium'
                                  : 'text-gray-700 hover:bg-gray-50' }}">
                        <span class="uppercase text-xs font-bold w-6">{{ $lang->code }}</span>
                        <span>{{ $lang->name }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
@endif
