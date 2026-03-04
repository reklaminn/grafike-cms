{{-- Module 282: 404 Not Found --}}
<div class="not-found py-16 text-center">
    {{-- 404 Icon --}}
    <div class="mb-8">
        <span class="text-8xl font-black text-gray-200">404</span>
    </div>

    <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $title }}</h1>
    <p class="text-lg text-gray-600 mb-8 max-w-lg mx-auto">{{ $message }}</p>

    {{-- Redirect suggestion --}}
    @if($redirect)
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-8 max-w-md mx-auto">
            <p class="text-sm text-blue-800">
                Bu sayfa taşınmış olabilir.
                <a href="{{ $redirect->new_url }}" class="font-semibold underline hover:text-blue-900">
                    Yeni adrese git &rarr;
                </a>
            </p>
        </div>
    @endif

    {{-- Suggested Pages --}}
    @if($suggestedPages->count() > 0)
        <div class="max-w-2xl mx-auto">
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Önerilen Sayfalar</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                @foreach($suggestedPages as $sugPage)
                    <a href="/{{ $sugPage->slug }}"
                       class="block px-4 py-3 bg-white rounded-lg border border-gray-200 hover:border-indigo-300 hover:shadow-sm text-sm text-gray-700 hover:text-indigo-600 transition-all">
                        {{ $sugPage->title }}
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Back Home Button --}}
    <div class="mt-10">
        <a href="/" class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Ana Sayfaya Dön
        </a>
    </div>
</div>
