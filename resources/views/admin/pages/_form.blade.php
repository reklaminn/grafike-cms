{{-- Shared page form partial for create and edit --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Main content column -->
    <div class="lg:col-span-2 space-y-6">

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

        <!-- Visual Layout Builder -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-base font-semibold text-gray-800 mb-4">
                <i class="fas fa-th-large mr-2 text-indigo-500"></i>Sayfa Düzeni (Layout Builder)
            </h3>
            <p class="text-xs text-gray-400 mb-3">Satır, kolon ve modüller ekleyerek sayfanın görsel düzenini oluşturun.</p>
            @include('admin.pages._layout-builder')
        </div>

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
    </div>

    <!-- Sidebar column -->
    <div class="space-y-6">

        <!-- Publish box -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-base font-semibold text-gray-800 mb-4">Yayın</h3>

            <div class="space-y-4">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Durum *</label>
                    <select id="status" name="status" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                        <option value="draft" {{ old('status', $page->status ?? 'draft') === 'draft' ? 'selected' : '' }}>Taslak</option>
                        <option value="published" {{ old('status', $page->status ?? '') === 'published' ? 'selected' : '' }}>Yayında</option>
                        <option value="archived" {{ old('status', $page->status ?? '') === 'archived' ? 'selected' : '' }}>Arşivlenmiş</option>
                    </select>
                </div>

                <div>
                    <label for="language_id" class="block text-sm font-medium text-gray-700 mb-1">Dil *</label>
                    <select id="language_id" name="language_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                        @foreach($languages as $lang)
                            <option value="{{ $lang->id }}" {{ old('language_id', $page->language_id ?? '') == $lang->id ? 'selected' : '' }}>
                                {{ $lang->name }} ({{ $lang->code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="parent_id" class="block text-sm font-medium text-gray-700 mb-1">Üst Sayfa</label>
                    <select id="parent_id" name="parent_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                        <option value="">— Ana Sayfa (Kök) —</option>
                        @foreach($parentPages as $parentPage)
                            <option value="{{ $parentPage->id }}" {{ old('parent_id', $page->parent_id ?? '') == $parentPage->id ? 'selected' : '' }}>
                                {{ $parentPage->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">Sıralama</label>
                    <input type="number" id="sort_order" name="sort_order" min="0"
                           value="{{ old('sort_order', $page->sort_order ?? 0) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>

                <div>
                    <label for="template" class="block text-sm font-medium text-gray-700 mb-1">Şablon</label>
                    <input type="text" id="template" name="template"
                           value="{{ old('template', $page->template ?? '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                           placeholder="default">
                </div>
            </div>

            <div class="mt-6 flex gap-3">
                <button type="submit"
                        class="flex-1 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                    <i class="fas fa-save mr-1"></i>
                    {{ isset($page) ? 'Güncelle' : 'Oluştur' }}
                </button>
                <a href="{{ route('admin.pages.index') }}"
                   class="px-4 py-2 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200 transition-colors">
                    İptal
                </a>
            </div>
        </div>

        <!-- Cover Image -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-base font-semibold text-gray-800 mb-4">Kapak Görseli</h3>

            @if(isset($page) && $page->getFirstMediaUrl('cover'))
                <div class="mb-4 relative group">
                    <img src="{{ $page->getFirstMediaUrl('cover') }}"
                         class="w-full rounded-lg object-cover max-h-48" alt="">
                </div>
            @endif

            <input type="file" id="cover_image" name="cover_image" accept="image/*"
                   class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
            <p class="mt-2 text-xs text-gray-400">Maks. 5MB (JPG, PNG, WebP)</p>
        </div>

        <!-- Options -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-base font-semibold text-gray-800 mb-4">Seçenekler</h3>

            <div class="space-y-3">
                <label class="flex items-center gap-2">
                    <input type="hidden" name="show_in_menu" value="0">
                    <input type="checkbox" name="show_in_menu" value="1"
                           {{ old('show_in_menu', $page->show_in_menu ?? true) ? 'checked' : '' }}
                           class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="text-sm text-gray-700">Menüde göster</span>
                </label>

                <label class="flex items-center gap-2">
                    <input type="hidden" name="show_breadcrumb" value="0">
                    <input type="checkbox" name="show_breadcrumb" value="1"
                           {{ old('show_breadcrumb', $page->show_breadcrumb ?? true) ? 'checked' : '' }}
                           class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="text-sm text-gray-700">Breadcrumb göster</span>
                </label>

                <label class="flex items-center gap-2">
                    <input type="hidden" name="show_social_share" value="0">
                    <input type="checkbox" name="show_social_share" value="1"
                           {{ old('show_social_share', $page->show_social_share ?? false) ? 'checked' : '' }}
                           class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="text-sm text-gray-700">Sosyal paylaşım butonları</span>
                </label>

                <label class="flex items-center gap-2">
                    <input type="hidden" name="show_facebook_comments" value="0">
                    <input type="checkbox" name="show_facebook_comments" value="1"
                           {{ old('show_facebook_comments', $page->show_facebook_comments ?? false) ? 'checked' : '' }}
                           class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="text-sm text-gray-700">Facebook yorumları</span>
                </label>

                <label class="flex items-center gap-2">
                    <input type="hidden" name="is_password_protected" value="0">
                    <input type="checkbox" name="is_password_protected" value="1"
                           {{ old('is_password_protected', $page->is_password_protected ?? false) ? 'checked' : '' }}
                           class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="text-sm text-gray-700">Şifre korumalı</span>
                </label>

                <div>
                    <label for="link_target" class="block text-sm font-medium text-gray-700 mb-1">Bağlantı Hedefi</label>
                    <select id="link_target" name="link_target"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                        <option value="_self" {{ old('link_target', $page->link_target ?? '_self') === '_self' ? 'selected' : '' }}>Aynı pencere</option>
                        <option value="_blank" {{ old('link_target', $page->link_target ?? '') === '_blank' ? 'selected' : '' }}>Yeni pencere</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>
