{{-- Module 1381: Corporate Menu --}}
<div class="corporate-menu">
    <div class="flex flex-wrap items-center justify-between gap-4">
        {{-- Left: Contact Info --}}
        <div class="flex flex-wrap items-center gap-4 text-sm">
            @if($phone)
                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $phone) }}" class="flex items-center text-gray-600 hover:text-indigo-600 transition-colors">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    {{ $phone }}
                </a>
            @endif
            @if($email)
                <a href="mailto:{{ $email }}" class="flex items-center text-gray-600 hover:text-indigo-600 transition-colors">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    {{ $email }}
                </a>
            @endif
        </div>

        {{-- Right: Social Links + Menu --}}
        <div class="flex items-center gap-3">
            {{-- Social Links --}}
            @foreach($socialLinks as $platform => $url)
                @if($url)
                    <a href="{{ $url }}" target="_blank" rel="noopener noreferrer"
                       class="text-gray-400 hover:text-indigo-600 transition-colors" title="{{ ucfirst($platform) }}">
                        @switch($platform)
                            @case('facebook')
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.77,7.46H14.5v-1.9c0-.9.6-1.1,1-1.1h3V.5h-4.33C10.24.5,9.5,3.44,9.5,5.32v2.15h-3v4h3v12h5v-12h3.85l.42-4Z"/></svg>
                                @break
                            @case('instagram')
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12,2.16c3.2,0,3.58.01,4.85.07,3.25.15,4.77,1.69,4.92,4.92.06,1.27.07,1.65.07,4.85s-.01,3.58-.07,4.85c-.15,3.23-1.66,4.77-4.92,4.92-1.27.06-1.65.07-4.85.07s-3.58-.01-4.85-.07c-3.26-.15-4.77-1.7-4.92-4.92-.06-1.27-.07-1.65-.07-4.85s.01-3.58.07-4.85C2.38,3.92,3.9,2.38,7.15,2.23,8.42,2.18,8.8,2.16,12,2.16ZM12,0C8.74,0,8.33.01,7.05.07,2.7.27.27,2.69.07,7.05.01,8.33,0,8.74,0,12s.01,3.67.07,4.95c.2,4.36,2.62,6.78,6.98,6.98C8.33,23.99,8.74,24,12,24s3.67-.01,4.95-.07c4.35-.2,6.78-2.62,6.98-6.98.06-1.28.07-1.69.07-4.95s-.01-3.67-.07-4.95c-.2-4.35-2.62-6.78-6.98-6.98C15.67.01,15.26,0,12,0Zm0,5.84A6.16,6.16,0,1,0,18.16,12,6.16,6.16,0,0,0,12,5.84ZM12,16a4,4,0,1,1,4-4A4,4,0,0,1,12,16ZM18.41,4.15a1.44,1.44,0,1,0,1.44,1.44A1.44,1.44,0,0,0,18.41,4.15Z"/></svg>
                                @break
                            @case('twitter')
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                                @break
                            @case('youtube')
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.5,6.19a3.02,3.02,0,0,0-2.12-2.14C19.5,3.5,12,3.5,12,3.5s-7.5,0-9.38.55A3.02,3.02,0,0,0,.5,6.19,31.56,31.56,0,0,0,0,12a31.56,31.56,0,0,0,.5,5.81,3.02,3.02,0,0,0,2.12,2.14c1.87.55,9.38.55,9.38.55s7.5,0,9.38-.55a3.02,3.02,0,0,0,2.12-2.14A31.56,31.56,0,0,0,24,12,31.56,31.56,0,0,0,23.5,6.19ZM9.55,15.57V8.43L15.82,12Z"/></svg>
                                @break
                            @case('linkedin')
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M20.45,20.45H16.89V14.88c0-1.33-.02-3.04-1.85-3.04-1.85,0-2.14,1.45-2.14,2.94v5.67H9.34V9h3.41v1.56h.05a3.74,3.74,0,0,1,3.37-1.85c3.6,0,4.27,2.37,4.27,5.46v6.28ZM5.34,7.43A2.06,2.06,0,1,1,7.4,5.37,2.06,2.06,0,0,1,5.34,7.43ZM7.12,20.45H3.56V9H7.12ZM22.22,0H1.77A1.75,1.75,0,0,0,0,1.73V22.27A1.75,1.75,0,0,0,1.77,24H22.22A1.75,1.75,0,0,0,24,22.27V1.73A1.75,1.75,0,0,0,22.22,0Z"/></svg>
                                @break
                        @endswitch
                    </a>
                @endif
            @endforeach

            {{-- Divider --}}
            @if($menuItems->count() > 0)
                <span class="text-gray-300">|</span>
            @endif

            {{-- Menu Items --}}
            @foreach($menuItems as $item)
                <a href="{{ $item->url ?: ($item->page ? '/' . $item->page->slug : '#') }}"
                   class="text-sm text-gray-600 hover:text-indigo-600 transition-colors
                          {{ $item->page?->slug === $currentSlug ? 'font-semibold text-indigo-600' : '' }}">
                    {{ $item->title }}
                </a>
            @endforeach
        </div>
    </div>
</div>
