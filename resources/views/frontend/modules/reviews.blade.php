{{-- Module 293: Reviews --}}
<div class="reviews-module" x-data="{ showForm: false, rating: 5 }">
    @if($title)
        <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ $title }}</h2>
    @endif

    {{-- Summary --}}
    @if($totalReviews > 0)
        <div class="flex items-center gap-4 mb-8 p-5 bg-gray-50 rounded-xl">
            <div class="text-center">
                <div class="text-4xl font-bold text-gray-900">{{ $averageRating }}</div>
                <div class="flex items-center justify-center mt-1">
                    @for($i = 1; $i <= 5; $i++)
                        <svg class="w-5 h-5 {{ $i <= round($averageRating) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @endfor
                </div>
                <div class="text-sm text-gray-500 mt-1">{{ $totalReviews }} yorum</div>
            </div>
        </div>
    @endif

    {{-- Review List --}}
    <div class="space-y-4 mb-8">
        @forelse($reviews as $review)
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="flex items-start justify-between">
                    <div>
                        <div class="font-semibold text-gray-800">{{ $review->author_name }}</div>
                        <div class="flex items-center mt-1">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>
                    </div>
                    <time class="text-xs text-gray-400">{{ $review->created_at->format('d.m.Y') }}</time>
                </div>
                @if($review->comment)
                    <p class="text-gray-600 mt-3 text-sm leading-relaxed">{{ $review->comment }}</p>
                @endif
            </div>
        @empty
            <div class="text-center py-8 text-gray-500">
                <p>Henüz yorum yapılmamış. İlk yorumu siz yapın!</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($reviews->hasPages())
        <div class="mb-8">{{ $reviews->links() }}</div>
    @endif

    {{-- Review Form --}}
    @if($showForm)
        <div class="bg-gray-50 rounded-xl p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Yorum Yap</h3>
            <form action="{{ route('reviews.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="reviewable_type" value="{{ $entityType === 'article' ? 'App\\Models\\Article' : 'App\\Models\\Page' }}">
                <input type="hidden" name="reviewable_id" value="{{ $entityId }}">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Adınız *</label>
                        <input type="text" name="author_name" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">E-posta</label>
                        <input type="email" name="author_email"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                {{-- Star Rating --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Puanınız</label>
                    <div class="flex items-center gap-1">
                        @for($i = 1; $i <= 5; $i++)
                            <button type="button" @click="rating = {{ $i }}"
                                    class="focus:outline-none">
                                <svg :class="rating >= {{ $i }} ? 'text-yellow-400' : 'text-gray-300'"
                                     class="w-8 h-8 hover:text-yellow-400 transition-colors cursor-pointer" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            </button>
                        @endfor
                    </div>
                    <input type="hidden" name="rating" :value="rating">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Yorumunuz *</label>
                    <textarea name="comment" rows="4" required
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                </div>

                @if($recaptchaKey)
                    <div class="g-recaptcha" data-sitekey="{{ $recaptchaKey }}"></div>
                @endif

                <button type="submit"
                        class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium">
                    Yorumu Gönder
                </button>
            </form>
        </div>
    @endif
</div>
