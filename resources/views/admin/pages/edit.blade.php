@extends('admin.layouts.app')

@section('title', 'Düzenle: ' . $page->title)
@section('page-title')
    Sayfayı Düzenle
    <span class="text-gray-400 font-normal text-sm ml-2">#{{ $page->id }}</span>
@endsection

@section('content')
    <form method="POST" action="{{ route('admin.pages.update', $page) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.pages._form')
    </form>

    {{-- Sub-pages section --}}
    @if($page->children->count() > 0)
        <div class="mt-8 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-base font-semibold text-gray-800 mb-4">
                <i class="fas fa-sitemap mr-2 text-indigo-500"></i>
                Alt Sayfalar ({{ $page->children->count() }})
            </h3>
            <div class="space-y-2">
                @foreach($page->children->sortBy('sort_order') as $child)
                    <div class="flex items-center justify-between px-4 py-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <span class="w-2 h-2 rounded-full {{ $child->status === 'published' ? 'bg-green-400' : 'bg-yellow-400' }}"></span>
                            <a href="{{ route('admin.pages.edit', $child) }}" class="text-sm text-gray-700 hover:text-indigo-600">
                                {{ $child->title }}
                            </a>
                        </div>
                        <span class="text-xs text-gray-400">/{{ $child->slug }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Articles section --}}
    <div class="mt-8 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-gray-800">
                <i class="fas fa-newspaper mr-2 text-green-500"></i>
                Bu Sayfadaki Yazılar
            </h3>
            <a href="{{ route('admin.articles.create', ['page_id' => $page->id]) }}"
               class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-50 text-green-700 text-xs font-medium rounded-lg hover:bg-green-100 transition-colors">
                <i class="fas fa-plus"></i> Yazı Ekle
            </a>
        </div>
        @php $pageArticles = $page->articles()->latest()->limit(10)->get(); @endphp
        @forelse($pageArticles as $article)
            <div class="flex items-center justify-between px-4 py-3 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                <div class="flex items-center gap-3">
                    <span class="w-2 h-2 rounded-full {{ $article->status === 'published' ? 'bg-green-400' : 'bg-yellow-400' }}"></span>
                    <a href="{{ route('admin.articles.edit', $article) }}" class="text-sm text-gray-700 hover:text-indigo-600">
                        {{ \Illuminate\Support\Str::limit($article->title, 50) }}
                    </a>
                </div>
                <span class="text-xs text-gray-400">{{ $article->created_at?->diffForHumans() }}</span>
            </div>
        @empty
            @php
                $usesArticleListSection = \App\Support\FrontendSections::containsType($page->sections_json, 'article-list');
            @endphp
            @if($usesArticleListSection && isset($siteArticles) && $siteArticles->count() > 0)
                <div class="space-y-3">
                    <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-xs text-amber-800">
                        Bu sayfa doğrudan <code>page->articles()</code> ilişkisini değil, <strong>article-list</strong> section'ını kullanıyor.
                        Yani frontend'de gördüğün kartlar bu sayfaya bağlı yazılar değil, aynı site içindeki seed edilmiş yazılardan geliyor.
                    </div>
                    @foreach($siteArticles as $article)
                        <div class="flex items-center justify-between px-4 py-3 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                            <div class="flex items-center gap-3">
                                <span class="w-2 h-2 rounded-full {{ $article->status === 'published' ? 'bg-green-400' : 'bg-yellow-400' }}"></span>
                                <a href="{{ route('admin.articles.edit', $article) }}" class="text-sm text-gray-700 hover:text-indigo-600">
                                    {{ \Illuminate\Support\Str::limit($article->title, 50) }}
                                </a>
                            </div>
                            <span class="text-xs text-gray-400">{{ $article->page?->title ?? '—' }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-400 py-4 text-center">Bu sayfada henüz yazı bulunmuyor.</p>
            @endif
        @endforelse
    </div>
@endsection
