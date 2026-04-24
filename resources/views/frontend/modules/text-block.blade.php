@php
    $alignmentClasses = [
        'left' => 'text-left',
        'center' => 'text-center mx-auto',
        'right' => 'text-right ml-auto',
    ];
    $maxWidthClasses = [
        'full' => 'max-w-none',
        '4xl' => 'max-w-4xl',
        '3xl' => 'max-w-3xl',
        '2xl' => 'max-w-2xl',
    ];
@endphp

<section class="text-block {{ $alignmentClasses[$align] ?? $alignmentClasses['left'] }} {{ $maxWidthClasses[$maxWidth] ?? $maxWidthClasses['3xl'] }}">
    @if($title)
        <h2 class="mb-4 text-3xl font-bold tracking-tight text-slate-900">{{ $title }}</h2>
    @endif

    @if($body)
        <div class="prose prose-slate max-w-none">
            {!! $body !!}
        </div>
    @endif
</section>
