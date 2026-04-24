<div class="spacer" style="--spacer-mobile: {{ $heightMobile }}px; --spacer-desktop: {{ $height }}px; height: var(--spacer-mobile);"></div>

@once
    @push('styles')
        <style>
            @media (min-width: 768px) {
                .spacer {
                    height: var(--spacer-desktop);
                }
            }
        </style>
    @endpush
@endonce
