@extends('admin.layouts.app')

@section('title', 'Yazılar')
@section('page-title', 'Yazılar')

@section('content')
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <p class="text-sm text-gray-500">Tüm yazıları ve makaleleri yönetin.</p>
        <a href="{{ route('admin.articles.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
            <i class="fas fa-plus"></i> Yeni Yazı
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <form method="GET" class="flex flex-col sm:flex-row items-end gap-4">
            <div class="flex-1 w-full">
                <label class="block text-xs font-medium text-gray-500 mb-1">Ara</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Yazı başlığı ile ara..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            </div>
            <div class="w-full sm:w-40">
                <label class="block text-xs font-medium text-gray-500 mb-1">Durum</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                    <option value="">Tümü</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Yayında</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Taslak</option>
                </select>
            </div>
            <div class="w-full sm:w-48">
                <label class="block text-xs font-medium text-gray-500 mb-1">Sayfa</label>
                <select name="page_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                    <option value="">Tüm Sayfalar</option>
                    @foreach($pages as $page)
                        <option value="{{ $page->id }}" {{ request('page_id') == $page->id ? 'selected' : '' }}>
                            {{ \Illuminate\Support\Str::limit($page->title, 30) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200">
                <i class="fas fa-search mr-1"></i> Filtrele
            </button>
        </form>
    </div>

    <!-- Articles Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Yazı</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sayfa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Yazar</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarih</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($articles as $article)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if($article->getFirstMediaUrl('cover', 'thumb'))
                                        <img src="{{ $article->getFirstMediaUrl('cover', 'thumb') }}"
                                             class="w-10 h-10 rounded-lg object-cover" alt="">
                                    @else
                                        <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-newspaper text-gray-400"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <a href="{{ route('admin.articles.edit', $article) }}"
                                           class="text-sm font-medium text-gray-900 hover:text-indigo-600">
                                            {{ \Illuminate\Support\Str::limit($article->title, 50) }}
                                        </a>
                                        @if($article->is_featured)
                                            <span class="ml-2 px-1.5 py-0.5 bg-yellow-100 text-yellow-700 text-xs rounded">
                                                <i class="fas fa-star text-xs"></i>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-600">
                                    {{ $article->page ? \Illuminate\Support\Str::limit($article->page->title, 25) : '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($article->status === 'published')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-400"></span> Yayında
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-yellow-400"></span> Taslak
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $article->author?->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $article->updated_at?->diffForHumans() }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.articles.edit', $article) }}"
                                       class="p-2 text-gray-400 hover:text-indigo-600" title="Düzenle">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.articles.destroy', $article) }}"
                                          onsubmit="return confirm('Bu yazıyı silmek istediğinize emin misiniz?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 text-gray-400 hover:text-red-600" title="Sil">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                <i class="fas fa-newspaper text-4xl mb-3"></i>
                                <p class="text-sm">Henüz yazı bulunmuyor.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($articles->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $articles->withQueryString()->links() }}
            </div>
        @endif
    </div>
@endsection
