@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Toplam Sayfa</p>
                    <p class="mt-1 text-3xl font-bold text-gray-900">{{ number_format($stats['total_pages'] ?? 0) }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-file-alt text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Toplam Yazı</p>
                    <p class="mt-1 text-3xl font-bold text-gray-900">{{ number_format($stats['total_articles'] ?? 0) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-newspaper text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Yayında</p>
                    <p class="mt-1 text-3xl font-bold text-gray-900">{{ number_format($stats['published_pages'] ?? 0) }}</p>
                </div>
                <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-globe text-indigo-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Yeni Mesajlar</p>
                    <p class="mt-1 text-3xl font-bold text-gray-900">{{ number_format($stats['new_submissions'] ?? 0) }}</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-envelope text-orange-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Hızlı İşlemler</h2>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <a href="{{ route('admin.pages.create') }}"
               class="flex flex-col items-center gap-2 p-4 rounded-lg border-2 border-dashed border-gray-200 hover:border-indigo-300 hover:bg-indigo-50 transition-colors">
                <i class="fas fa-plus-circle text-2xl text-indigo-500"></i>
                <span class="text-sm font-medium text-gray-700">Yeni Sayfa</span>
            </a>
            <a href="{{ route('admin.articles.create') }}"
               class="flex flex-col items-center gap-2 p-4 rounded-lg border-2 border-dashed border-gray-200 hover:border-green-300 hover:bg-green-50 transition-colors">
                <i class="fas fa-pen-fancy text-2xl text-green-500"></i>
                <span class="text-sm font-medium text-gray-700">Yeni Yazı</span>
            </a>
            <a href="{{ route('admin.menus.index') }}"
               class="flex flex-col items-center gap-2 p-4 rounded-lg border-2 border-dashed border-gray-200 hover:border-purple-300 hover:bg-purple-50 transition-colors">
                <i class="fas fa-bars text-2xl text-purple-500"></i>
                <span class="text-sm font-medium text-gray-700">Menü Düzenle</span>
            </a>
            <a href="{{ route('admin.forms.index') }}"
               class="flex flex-col items-center gap-2 p-4 rounded-lg border-2 border-dashed border-gray-200 hover:border-orange-300 hover:bg-orange-50 transition-colors">
                <i class="fas fa-inbox text-2xl text-orange-500"></i>
                <span class="text-sm font-medium text-gray-700">Form Mesajları</span>
            </a>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Pages -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Son Güncellenen Sayfalar</h2>
            @php $recentPages = \App\Models\Page::latest('updated_at')->limit(5)->get(); @endphp
            @forelse($recentPages as $page)
                <div class="flex items-center justify-between py-3 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                    <div class="flex items-center gap-3">
                        <span class="w-2 h-2 rounded-full {{ $page->status === 'published' ? 'bg-green-400' : 'bg-yellow-400' }}"></span>
                        <a href="{{ route('admin.pages.edit', $page) }}" class="text-sm text-gray-700 hover:text-indigo-600">
                            {{ \Illuminate\Support\Str::limit($page->title, 40) }}
                        </a>
                    </div>
                    <span class="text-xs text-gray-400">{{ $page->updated_at?->diffForHumans() }}</span>
                </div>
            @empty
                <p class="text-sm text-gray-400 py-4 text-center">Henüz sayfa bulunmuyor.</p>
            @endforelse
        </div>

        <!-- Recent Articles -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Son Eklenen Yazılar</h2>
            @php $recentArticles = \App\Models\Article::latest('created_at')->limit(5)->get(); @endphp
            @forelse($recentArticles as $article)
                <div class="flex items-center justify-between py-3 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                    <div class="flex items-center gap-3">
                        <span class="w-2 h-2 rounded-full {{ $article->status === 'published' ? 'bg-green-400' : 'bg-yellow-400' }}"></span>
                        <a href="{{ route('admin.articles.edit', $article) }}" class="text-sm text-gray-700 hover:text-indigo-600">
                            {{ \Illuminate\Support\Str::limit($article->title, 40) }}
                        </a>
                    </div>
                    <span class="text-xs text-gray-400">{{ $article->created_at?->diffForHumans() }}</span>
                </div>
            @empty
                <p class="text-sm text-gray-400 py-4 text-center">Henüz yazı bulunmuyor.</p>
            @endforelse
        </div>
    </div>
@endsection
