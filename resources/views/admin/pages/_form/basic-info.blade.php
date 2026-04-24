<!-- Basic Info -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <h3 class="text-base font-semibold text-gray-800 mb-4">Temel Bilgiler</h3>

    <div class="space-y-4">
        <div>
            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Sayfa Başlığı *</label>
            <input type="text" id="title" name="title" required
                   value="{{ old('title', $page->title ?? '') }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                   placeholder="Sayfa başlığını girin">
        </div>

        <div>
            <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">
                URL Slug
                <span class="text-gray-400 font-normal">(boş bırakılırsa otomatik oluşturulur)</span>
            </label>
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-400">/</span>
                <input type="text" id="slug" name="slug"
                       value="{{ old('slug', $page->slug ?? '') }}"
                       class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                       placeholder="sayfa-url-slug">
            </div>
        </div>

        <div>
            <label for="external_url" class="block text-sm font-medium text-gray-700 mb-1">Dış Bağlantı (URL)</label>
            <input type="url" id="external_url" name="external_url"
                   value="{{ old('external_url', $page->external_url ?? '') }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                   placeholder="https://...">
        </div>
    </div>
</div>
