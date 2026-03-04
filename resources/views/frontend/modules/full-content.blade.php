{{-- Module 115: Full Content --}}
<div class="full-content">
    @if($title)
        <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ $title }}</h2>
    @endif

    @if($excerpt)
        <p class="text-lg text-gray-600 mb-6 leading-relaxed">{{ $excerpt }}</p>
    @endif

    @if($body)
        <div class="prose prose-lg max-w-none">
            {!! $body !!}
        </div>
    @endif
</div>
