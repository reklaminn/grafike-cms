{{-- Breadcrumbs Component --}}
@if(isset($breadcrumbs) && count($breadcrumbs) > 0)
    <nav aria-label="Breadcrumb">
        <ol class="flex flex-wrap items-center gap-1.5 text-sm text-gray-500" itemscope itemtype="https://schema.org/BreadcrumbList">
            <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <a href="/" itemprop="item" class="hover:text-indigo-600 transition-colors">
                    <span itemprop="name">Ana Sayfa</span>
                </a>
                <meta itemprop="position" content="1">
            </li>
            @foreach($breadcrumbs as $index => $crumb)
                <li class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    <span itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                        @if($loop->last)
                            <span itemprop="name" class="font-medium text-gray-800">{{ $crumb['title'] }}</span>
                        @else
                            <a href="/{{ $crumb['slug'] }}" itemprop="item" class="hover:text-indigo-600 transition-colors">
                                <span itemprop="name">{{ $crumb['title'] }}</span>
                            </a>
                        @endif
                        <meta itemprop="position" content="{{ $index + 2 }}">
                    </span>
                </li>
            @endforeach
        </ol>
    </nav>
@endif
