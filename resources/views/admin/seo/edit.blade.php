@extends('admin.layouts.app')
@section('title', 'SEO Düzenle')

@section('content')
<div class="max-w-3xl">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">SEO Düzenle</h1>
        <a href="{{ route('admin.seo.index') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Geri</a>
    </div>

    {{-- Entity Info --}}
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6 text-sm">
        <strong>İlişkili:</strong>
        {{ str_contains($seoEntry->seoable_type, 'Page') ? 'Sayfa' : 'Yazı' }} -
        {{ $seoEntry->seoable?->title ?? 'Silinmiş' }}
    </div>

    {{-- SERP Preview --}}
    <div class="bg-white rounded-xl shadow-sm border p-6 mb-6" x-data="{
        title: '{{ addslashes(old('meta_title', $seoEntry->meta_title ?? $seoEntry->seoable?->title ?? '')) }}',
        desc: '{{ addslashes(old('meta_description', $seoEntry->meta_description ?? '')) }}',
        slug: '{{ old('slug', $seoEntry->slug ?? '') }}'
    }">
        <h3 class="text-sm font-semibold text-gray-800 mb-3">
            <i class="fab fa-google mr-1 text-blue-500"></i> Google SERP Önizleme
        </h3>
        <div class="bg-white border rounded-lg p-4 max-w-2xl">
            <div class="text-lg text-blue-800 hover:underline cursor-pointer truncate" x-text="title || 'Sayfa Başlığı'" style="font-family: arial, sans-serif;"></div>
            <div class="text-sm text-green-700 mt-0.5" style="font-family: arial, sans-serif;" x-text="'{{ url('/') }}/' + slug"></div>
            <div class="text-sm text-gray-600 mt-1 line-clamp-2" style="font-family: arial, sans-serif;" x-text="desc || 'Meta açıklama buraya gelecek...'"></div>
        </div>
        <div class="flex gap-4 mt-3 text-xs text-gray-400">
            <span>Başlık: <strong :class="title.length > 60 ? 'text-red-500' : 'text-green-600'" x-text="title.length + '/60'"></strong></span>
            <span>Açıklama: <strong :class="desc.length > 160 ? 'text-red-500' : 'text-green-600'" x-text="desc.length + '/160'"></strong></span>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.seo.update', $seoEntry) }}" class="bg-white rounded-xl shadow-sm border p-6 space-y-5"
          x-data x-on:input="
              if ($event.target.name === 'meta_title') { document.querySelector('[x-data]').dispatchEvent(new CustomEvent('update-title', {detail: $event.target.value})); }
              if ($event.target.name === 'meta_description') { document.querySelector('[x-data]').dispatchEvent(new CustomEvent('update-desc', {detail: $event.target.value})); }
              if ($event.target.name === 'slug') { document.querySelector('[x-data]').dispatchEvent(new CustomEvent('update-slug', {detail: $event.target.value})); }
          ">
        @csrf @method('PUT')

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Slug (URL)</label>
            <div class="flex items-center gap-2">
                <span class="text-gray-400 text-sm">{{ url('/') }}/</span>
                <input type="text" name="slug" value="{{ old('slug', $seoEntry->slug) }}" required
                       class="flex-1 px-3 py-2 border rounded-lg text-sm focus:ring-indigo-500">
            </div>
            @error('slug') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Meta Title <span class="text-gray-400 font-normal" x-data x-text="'(' + ($refs.metaTitle?.value?.length || 0) + '/70)'"></span>
            </label>
            <input type="text" name="meta_title" value="{{ old('meta_title', $seoEntry->meta_title) }}" maxlength="70" x-ref="metaTitle"
                   class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-indigo-500" placeholder="Sayfa başlığı (max 70 karakter)">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Meta Description <span class="text-gray-400 font-normal" x-data x-text="'(' + ($refs.metaDesc?.value?.length || 0) + '/160)'"></span>
            </label>
            <textarea name="meta_description" rows="3" maxlength="160" x-ref="metaDesc"
                      class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-indigo-500" placeholder="Açıklama (max 160 karakter)">{{ old('meta_description', $seoEntry->meta_description) }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Meta Keywords</label>
            <input type="text" name="meta_keywords" value="{{ old('meta_keywords', $seoEntry->meta_keywords) }}"
                   class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-indigo-500" placeholder="Anahtar kelimeler (virgülle ayırın)">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">H1 Override</label>
            <input type="text" name="h1_override" value="{{ old('h1_override', $seoEntry->h1_override) }}"
                   class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-indigo-500" placeholder="Boş bırakılırsa sayfa başlığı kullanılır">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Canonical URL</label>
            <input type="url" name="canonical_url" value="{{ old('canonical_url', $seoEntry->canonical_url) }}"
                   class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-indigo-500" placeholder="https://...">
        </div>

        <div class="flex items-center gap-2">
            <input type="hidden" name="is_noindex" value="0">
            <input type="checkbox" name="is_noindex" value="1" id="is_noindex"
                   {{ old('is_noindex', $seoEntry->is_noindex) ? 'checked' : '' }} class="rounded text-indigo-600">
            <label for="is_noindex" class="text-sm text-gray-700">Noindex (Arama motorlarından gizle)</label>
        </div>

        {{-- Advanced --}}
        <details class="border rounded-lg">
            <summary class="px-4 py-3 cursor-pointer text-sm font-medium text-gray-700 hover:bg-gray-50">Gelişmiş Ayarlar</summary>
            <div class="p-4 space-y-4 border-t">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sayfa CSS</label>
                    <textarea name="page_css" rows="4" class="w-full px-3 py-2 border rounded-lg text-sm font-mono focus:ring-indigo-500">{{ old('page_css', $seoEntry->page_css) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sayfa JS</label>
                    <textarea name="page_js" rows="4" class="w-full px-3 py-2 border rounded-lg text-sm font-mono focus:ring-indigo-500">{{ old('page_js', $seoEntry->page_js) }}</textarea>
                </div>
            </div>
        </details>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">Kaydet</button>
            <a href="{{ route('admin.seo.index') }}" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200">İptal</a>
        </div>
    </form>
</div>
@endsection
