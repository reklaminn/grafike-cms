@php
    $ratioClasses = [
        '16:9' => 'aspect-video',
        '4:3' => 'aspect-[4/3]',
        '1:1' => 'aspect-square',
    ];
@endphp

<section class="video-embed">
    @if($title)
        <h2 class="mb-4 text-2xl font-bold text-slate-900">{{ $title }}</h2>
    @endif

    @if($embedUrl)
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-slate-950 {{ $ratioClasses[$aspectRatio] ?? $ratioClasses['16:9'] }}">
            <iframe
                src="{{ $embedUrl }}"
                class="h-full w-full"
                loading="lazy"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                referrerpolicy="strict-origin-when-cross-origin"
                allowfullscreen
            ></iframe>
        </div>
    @else
        <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-6 py-10 text-sm text-slate-500">
            Video URL girilmedi.
        </div>
    @endif
</section>
