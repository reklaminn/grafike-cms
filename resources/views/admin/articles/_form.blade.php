{{-- Shared article form partial --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">

        <!-- Basic Info -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-base font-semibold text-gray-800 mb-4">Temel Bilgiler</h3>
            <div class="space-y-4">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Yazı Başlığı *</label>
                    <input type="text" id="title" name="title" required
                           value="{{ old('title', $article->title ?? '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div>
                    <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">URL Slug</label>
                    <input type="text" id="slug" name="slug"
                           value="{{ old('slug', $article->slug ?? '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                           placeholder="otomatik-olusturulur">
                </div>
                <div>
                    <label for="excerpt" class="block text-sm font-medium text-gray-700 mb-1">Kısa Özet</label>
                    <textarea id="excerpt" name="excerpt" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    >{{ old('excerpt', $article->excerpt ?? '') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Content Editor -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-base font-semibold text-gray-800 mb-4">İçerik</h3>
            <textarea id="body" name="body" rows="20"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm font-mono focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            >{{ old('body', $article->body ?? '') }}</textarea>
            <p class="mt-2 text-xs text-gray-400">HTML desteklenir. TinyMCE entegrasyonu Faz 4'te eklenecek.</p>
        </div>

        <!-- Extra Info -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-base font-semibold text-gray-800 mb-4">Ek Bilgiler</h3>
            <textarea id="extra_info" name="extra_info" rows="4"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            >{{ old('extra_info', $article->extra_info ?? '') }}</textarea>
        </div>

        <!-- SEO -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6" x-data="{ open: {{ isset($article) && $article->seo ? 'true' : 'false' }} }">
            <button type="button" @click="open = !open" class="flex items-center justify-between w-full">
                <h3 class="text-base font-semibold text-gray-800">SEO Ayarları</h3>
                <i :class="open ? 'fa-chevron-up' : 'fa-chevron-down'" class="fas text-gray-400"></i>
            </button>
            <div x-show="open" x-collapse class="mt-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Meta Başlık</label>
                    <input type="text" name="seo_title"
                           value="{{ old('seo_title', $article->seo?->meta_title ?? '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Meta Açıklama</label>
                    <textarea name="seo_description" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    >{{ old('seo_description', $article->seo?->meta_description ?? '') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Anahtar Kelimeler</label>
                    <input type="text" name="seo_keywords"
                           value="{{ old('seo_keywords', $article->seo?->meta_keywords ?? '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">

        <!-- Publish box -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-base font-semibold text-gray-800 mb-4">Yayın</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Durum *</label>
                    <select name="status" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                        <option value="draft" {{ old('status', $article->status ?? 'draft') === 'draft' ? 'selected' : '' }}>Taslak</option>
                        <option value="published" {{ old('status', $article->status ?? '') === 'published' ? 'selected' : '' }}>Yayında</option>
                        <option value="archived" {{ old('status', $article->status ?? '') === 'archived' ? 'selected' : '' }}>Arşivlenmiş</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dil *</label>
                    <select name="language_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                        @foreach($languages as $lang)
                            <option value="{{ $lang->id }}" {{ old('language_id', $article->language_id ?? '') == $lang->id ? 'selected' : '' }}>
                                {{ $lang->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bağlı Sayfa</label>
                    <select name="page_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                        <option value="">— Seçiniz —</option>
                        @foreach($pages as $page)
                            <option value="{{ $page->id }}" {{ old('page_id', $article->page_id ?? $selectedPageId ?? '') == $page->id ? 'selected' : '' }}>
                                {{ \Illuminate\Support\Str::limit($page->title, 40) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Form</label>
                    <select name="form_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                        <option value="">— Form yok —</option>
                        @foreach($forms as $form)
                            <option value="{{ $form->id }}" {{ old('form_id', $article->form_id ?? '') == $form->id ? 'selected' : '' }}>
                                {{ $form->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sıralama</label>
                    <input type="number" name="sort_order" min="0"
                           value="{{ old('sort_order', $article->sort_order ?? 0) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Yayın Tarihi</label>
                    <input type="datetime-local" name="published_at"
                           value="{{ old('published_at', isset($article) && $article->published_at ? $article->published_at->format('Y-m-d\TH:i') : '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <label class="flex items-center gap-2">
                    <input type="hidden" name="is_featured" value="0">
                    <input type="checkbox" name="is_featured" value="1"
                           {{ old('is_featured', $article->is_featured ?? false) ? 'checked' : '' }}
                           class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="text-sm text-gray-700">Öne çıkan yazı</span>
                </label>
            </div>

            <div class="mt-6 flex gap-3">
                <button type="submit"
                        class="flex-1 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                    <i class="fas fa-save mr-1"></i> {{ isset($article) ? 'Güncelle' : 'Oluştur' }}
                </button>
                <a href="{{ route('admin.articles.index') }}"
                   class="px-4 py-2 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200">İptal</a>
            </div>
        </div>

        <!-- Cover Image -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-base font-semibold text-gray-800 mb-4">Kapak Görseli</h3>
            @if(isset($article) && $article->getFirstMediaUrl('cover'))
                <img src="{{ $article->getFirstMediaUrl('cover') }}"
                     class="w-full rounded-lg object-cover max-h-48 mb-4" alt="">
            @endif
            <input type="file" name="cover_image" accept="image/*"
                   class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
        </div>
    </div>
</div>
