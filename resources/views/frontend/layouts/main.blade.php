<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- SEO Meta --}}
    <title>{{ $seo->meta_title ?? $page->title ?? config('cms.name') }}{{ config('cms.seo.default_title_suffix') ? ' - ' . config('cms.seo.default_title_suffix') : '' }}</title>

    @if($seo->meta_description ?? false)
        <meta name="description" content="{{ $seo->meta_description }}">
    @endif

    @if($seo->meta_keywords ?? false)
        <meta name="keywords" content="{{ $seo->meta_keywords }}">
    @endif

    @if($seo->is_noindex ?? false)
        <meta name="robots" content="noindex, nofollow">
    @else
        <meta name="robots" content="index, follow">
    @endif

    @if($seo->canonical_url ?? false)
        <link rel="canonical" href="{{ $seo->canonical_url }}">
    @else
        <link rel="canonical" href="{{ url()->current() }}">
    @endif

    {{-- Hreflang Tags --}}
    @if(config('cms.seo.enable_hreflang') && ($seo->hreflang_tags ?? false))
        @foreach($seo->hreflang_tags as $lang => $url)
            <link rel="alternate" hreflang="{{ $lang }}" href="{{ $url }}">
        @endforeach
    @endif

    {{-- Open Graph --}}
    <meta property="og:type" content="{{ $article ? 'article' : 'website' }}">
    <meta property="og:title" content="{{ $seo->meta_title ?? $page->title ?? '' }}">
    <meta property="og:url" content="{{ url()->current() }}">
    @if($seo->meta_description ?? false)
        <meta property="og:description" content="{{ $seo->meta_description }}">
    @endif
    @if($page->getFirstMediaUrl('cover'))
        <meta property="og:image" content="{{ $page->getFirstMediaUrl('cover') }}">
    @elseif($article?->getFirstMediaUrl('cover'))
        <meta property="og:image" content="{{ $article->getFirstMediaUrl('cover') }}">
    @endif

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    @if(config('cms.social.twitter_username'))
        <meta name="twitter:site" content="{{ '@' . config('cms.social.twitter_username') }}">
    @endif

    {{-- Favicon --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    {{-- Google Tag Manager --}}
    @if(config('cms.analytics.google_tag_manager_id'))
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','{{ config('cms.analytics.google_tag_manager_id') }}');</script>
    @endif

    {{-- Page-specific CSS --}}
    @if($seo->page_css ?? false)
        <style>{!! $seo->page_css !!}</style>
    @endif

    {{-- Vite Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('head')
</head>
<body class="antialiased bg-white text-gray-900" x-data="{ mobileMenuOpen: false }">
    {{-- GTM noscript --}}
    @if(config('cms.analytics.google_tag_manager_id'))
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ config('cms.analytics.google_tag_manager_id') }}"
        height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    @endif

    {{-- Flash Messages --}}
    @if(session('success'))
        <div data-dismiss="auto" class="fixed top-4 right-4 z-50 max-w-sm bg-green-50 border border-green-200 text-green-800 rounded-lg p-4 shadow-lg">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <p class="text-sm">{{ session('success') }}</p>
            </div>
        </div>
    @endif
    @if(session('error'))
        <div data-dismiss="auto" class="fixed top-4 right-4 z-50 max-w-sm bg-red-50 border border-red-200 text-red-800 rounded-lg p-4 shadow-lg">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <p class="text-sm">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    {{-- Rendered Layout (from LayoutRenderer) --}}
    {!! $renderedLayout !!}

    {{-- Social Share --}}
    @if($page->show_social_share)
        @include('frontend.components.social-share', ['url' => url()->current(), 'title' => $page->title])
    @endif

    {{-- Back to Top --}}
    <button id="back-to-top" class="fixed bottom-6 left-6 z-40 w-10 h-10 bg-gray-800 text-white rounded-full shadow-lg opacity-0 pointer-events-none transition-all hover:bg-gray-700 flex items-center justify-center">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
        </svg>
    </button>

    {{-- Page-specific JS --}}
    @if($seo->page_js ?? false)
        <script>{!! $seo->page_js !!}</script>
    @endif

    {{-- Google Analytics --}}
    @if(config('cms.analytics.google_analytics_id'))
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('cms.analytics.google_analytics_id') }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{{ config('cms.analytics.google_analytics_id') }}');
        </script>
    @endif

    {{-- reCAPTCHA --}}
    @if(config('cms.recaptcha.enabled'))
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @endif

    @stack('scripts')
</body>
</html>
