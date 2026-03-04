{{-- Module 90: Content Block --}}
<div class="content-block">
    @if($isProtected)
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-8 text-center">
            <i class="fas fa-lock text-3xl text-yellow-500 mb-3"></i>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Bu sayfa şifre korumalıdır</h3>
            <form method="POST" action="{{ route('pages.unlock', $page) }}" class="max-w-xs mx-auto mt-4">
                @csrf
                <input type="password" name="page_password" placeholder="Şifre giriniz"
                       class="w-full px-4 py-2 border rounded-lg text-sm mb-3">
                <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">Giriş</button>
            </form>
        </div>
    @else
        @if($coverImage)
            <div class="mb-6">
                <img src="{{ $coverImage }}" alt="{{ $title }}" class="w-full rounded-lg object-cover max-h-96">
            </div>
        @endif

        <div class="prose prose-lg max-w-none">
            {!! $body !!}
        </div>

        @if($images->count() > 0)
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-8">
                @foreach($images as $image)
                    <a href="{{ $image->getUrl() }}" class="block" data-lightbox="gallery">
                        <img src="{{ $image->getUrl('thumb') ?: $image->getUrl() }}"
                             alt="{{ $image->getCustomProperty('alt_text', $title) }}"
                             class="w-full h-40 object-cover rounded-lg hover:opacity-90 transition-opacity">
                    </a>
                @endforeach
            </div>
        @endif
    @endif
</div>
