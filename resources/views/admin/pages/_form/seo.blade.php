<!-- SEO -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6" x-data="{ open: {{ isset($page) && $page->seo ? 'true' : 'false' }} }">
    <button type="button" @click="open = !open" class="flex items-center justify-between w-full">
        <h3 class="text-base font-semibold text-gray-800">SEO Ayarları</h3>
        <i :class="open ? 'fa-chevron-up' : 'fa-chevron-down'" class="fas text-gray-400"></i>
    </button>

    <div x-show="open" x-collapse class="mt-4 space-y-4">
        <div>
            <label for="seo_title" class="block text-sm font-medium text-gray-700 mb-1">Meta Başlık</label>
            <input type="text" id="seo_title" name="seo_title"
                   value="{{ old('seo_title', $page->seo?->meta_title ?? '') }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                   maxlength="70">
            <p class="mt-1 text-xs text-gray-400">Maks. 70 karakter önerilir</p>
        </div>

        <div>
            <label for="seo_description" class="block text-sm font-medium text-gray-700 mb-1">Meta Açıklama</label>
            <textarea id="seo_description" name="seo_description" rows="3"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                      maxlength="160"
            >{{ old('seo_description', $page->seo?->meta_description ?? '') }}</textarea>
            <p class="mt-1 text-xs text-gray-400">Maks. 160 karakter önerilir</p>
        </div>

        <div>
            <label for="seo_keywords" class="block text-sm font-medium text-gray-700 mb-1">Anahtar Kelimeler</label>
            <input type="text" id="seo_keywords" name="seo_keywords"
                   value="{{ old('seo_keywords', $page->seo?->meta_keywords ?? '') }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                   placeholder="kelime1, kelime2, kelime3">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="seo_h1" class="block text-sm font-medium text-gray-700 mb-1">H1 Geçersiz Kıl</label>
                <input type="text" id="seo_h1" name="seo_h1"
                       value="{{ old('seo_h1', $page->seo?->h1_override ?? '') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            </div>
            <div>
                <label for="seo_canonical" class="block text-sm font-medium text-gray-700 mb-1">Canonical URL</label>
                <input type="url" id="seo_canonical" name="seo_canonical"
                       value="{{ old('seo_canonical', $page->seo?->canonical_url ?? '') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            </div>
        </div>

        <div class="flex items-center gap-2">
            <input type="hidden" name="seo_noindex" value="0">
            <input type="checkbox" id="seo_noindex" name="seo_noindex" value="1"
                   {{ old('seo_noindex', $page->seo?->is_noindex ?? false) ? 'checked' : '' }}
                   class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
            <label for="seo_noindex" class="text-sm text-gray-700">noindex (Arama motorlarından gizle)</label>
        </div>
    </div>
</div>
