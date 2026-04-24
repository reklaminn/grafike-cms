@php
    $themeClasses = [
        'brand' => 'bg-gradient-to-r from-cyan-600 via-sky-700 to-slate-900 text-white',
        'dark' => 'bg-slate-950 text-white',
        'light' => 'bg-slate-100 text-slate-900',
    ];
@endphp

<section class="cta-banner rounded-3xl px-6 py-10 md:px-10 md:py-12 {{ $themeClasses[$theme] ?? $themeClasses['brand'] }}">
    <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
        <div class="max-w-3xl">
            <h2 class="text-3xl font-black tracking-tight">{{ $title }}</h2>
            @if($body)
                <div class="mt-3 text-sm leading-7 opacity-90 md:text-base">
                    {!! nl2br(e($body)) !!}
                </div>
            @endif
        </div>

        <div class="flex flex-wrap items-center gap-3">
            @if($buttonText)
                <a href="{{ $buttonUrl }}" class="inline-flex items-center rounded-full bg-white px-5 py-3 text-sm font-semibold text-slate-900 transition hover:bg-slate-100">
                    {{ $buttonText }}
                </a>
            @endif

            @if($secondaryText && $secondaryUrl)
                <a href="{{ $secondaryUrl }}" class="inline-flex items-center rounded-full border border-current/25 px-5 py-3 text-sm font-semibold transition hover:bg-white/10">
                    {{ $secondaryText }}
                </a>
            @endif
        </div>
    </div>
</section>
