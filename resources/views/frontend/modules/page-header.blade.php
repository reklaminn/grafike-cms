{{-- Module 110: Page Header --}}
<div class="page-header py-8 {{ $backgroundImage ? 'bg-cover bg-center text-white' : 'bg-gray-50' }}"
     @if($backgroundImage) style="background-image: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('{{ $backgroundImage }}')" @endif>
    <div class="container mx-auto px-4">
        @if($showBreadcrumb && count($breadcrumbs) > 0)
            <nav class="text-sm mb-3 {{ $backgroundImage ? 'text-gray-200' : 'text-gray-500' }}">
                <a href="/" class="hover:underline">Ana Sayfa</a>
                @foreach($breadcrumbs as $crumb)
                    <span class="mx-1">/</span>
                    @if(!$loop->last)
                        <a href="{{ $crumb['url'] }}" class="hover:underline">{{ $crumb['title'] }}</a>
                    @else
                        <span class="{{ $backgroundImage ? 'text-white' : 'text-gray-800' }} font-medium">{{ $crumb['title'] }}</span>
                    @endif
                @endforeach
            </nav>
        @endif

        <h1 class="text-3xl font-bold {{ $backgroundImage ? 'text-white' : 'text-gray-900' }}">
            {{ $h1Override ?? $title }}
        </h1>

        @if($subtitle)
            <p class="mt-2 text-lg {{ $backgroundImage ? 'text-gray-200' : 'text-gray-600' }}">{{ $subtitle }}</p>
        @endif
    </div>
</div>
