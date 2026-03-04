@extends('admin.layouts.app')
@section('title', 'SEO Yönetimi')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">SEO Yönetimi</h1>
    <a href="{{ route('admin.seo.analysis') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">
        <i class="fas fa-chart-bar mr-1"></i> SEO Analizi
    </a>
</div>

{{-- Search --}}
<form method="GET" class="mb-6">
    <div class="flex gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Slug veya başlık ara..."
               class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
        <label class="flex items-center gap-2 text-sm text-gray-600">
            <input type="checkbox" name="noindex" value="1" {{ request('noindex') ? 'checked' : '' }} class="rounded">
            Sadece Noindex
        </label>
        <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-lg text-sm">Ara</button>
    </div>
</form>

{{-- Table --}}
<div class="bg-white rounded-xl shadow-sm border overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Slug</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Meta Title</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Tür</th>
                <th class="text-center px-4 py-3 font-medium text-gray-600">Noindex</th>
                <th class="text-right px-4 py-3 font-medium text-gray-600">İşlemler</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($seoEntries as $entry)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <a href="/{{ $entry->slug }}" target="_blank" class="text-indigo-600 hover:underline font-mono text-xs">/{{ $entry->slug }}</a>
                    </td>
                    <td class="px-4 py-3">
                        <span class="{{ $entry->meta_title ? '' : 'text-red-400 italic' }}">
                            {{ $entry->meta_title ?: 'Tanımlı değil' }}
                        </span>
                        @if($entry->meta_title && mb_strlen($entry->meta_title) > 60)
                            <span class="text-orange-500 text-xs ml-1" title="60 karakterden uzun">⚠</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded-full text-xs {{ str_contains($entry->seoable_type, 'Page') ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }}">
                            {{ str_contains($entry->seoable_type, 'Page') ? 'Sayfa' : 'Yazı' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($entry->is_noindex)
                            <span class="text-red-500 text-xs font-semibold">NOINDEX</span>
                        @else
                            <span class="text-green-500 text-xs">INDEX</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('admin.seo.edit', $entry) }}" class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">Düzenle</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">SEO kaydı bulunamadı.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $seoEntries->links() }}</div>
@endsection
