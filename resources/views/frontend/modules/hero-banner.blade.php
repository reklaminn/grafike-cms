@php
    $alignmentClasses = [
        'left' => 'items-start text-left',
        'center' => 'items-center text-center',
        'right' => 'items-end text-right',
    ];
    $themeClasses = [
        'dark' => 'bg-slate-950 text-white',
        'light' => 'bg-slate-100 text-slate-900',
        'brand' => 'bg-gradient-to-br from-cyan-600 via-sky-700 to-slate-900 text-white',
    ];
@endphp

<section class="hero-banner relative overflow-hidden rounded-3xl {{ $themeClasses[$theme] ?? $themeClasses['dark'] }}">
    @if($backgroundImage)
        <div class="absolute inset-0">
            <img src="{{ $backgroundImage }}" alt="{{ $title }}" class="h-full w-full object-cover opacity-35">
        </div>
        <div class="absolute inset-0 bg-black/45"></div>
    @endif

    <div class="relative px-6 py-16 md:px-10 md:py-24">
        <div class="flex max-w-4xl flex-col gap-5 {{ $alignmentClasses[$align] ?? $alignmentClasses['left'] }}">
            <h2 class="text-4xl font-black tracking-tight md:text-6xl">{{ $title }}</h2>

            @if($subtitle)
                <div class="max-w-2xl text-base leading-7 text-white/85 md:text-lg">
                    {!! nl2br(e($subtitle)) !!}
                </div>
            @endif

            @if($buttonText && $buttonUrl)
                <div class="pt-2">
                    <a href="{{ $buttonUrl }}" class="inline-flex items-center rounded-full bg-white px-6 py-3 text-sm font-semibold text-slate-900 transition hover:bg-slate-100">
                        {{ $buttonText }}
                    </a>
                </div>
            @endif
        </div>
    </div>
</section>
