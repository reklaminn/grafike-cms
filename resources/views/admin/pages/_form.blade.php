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
            @if(isset($page) && !empty($page->sections_json))
                <div class="mb-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                    <div class="font-semibold">Bu sayfa yeni Frontend Section Mode kullanıyor.</div>
                    <p class="mt-1 text-xs leading-5 text-amber-700">
                        Aşağıdaki klasik Layout Builder şu an sadece eski <code>layout_json</code> yapısını düzenler.
                        Next.js frontend ise bu sayfayı <code>sections_json</code> ve <code>section_templates</code> üzerinden render ediyor.
                    </p>
                </div>
            @endif
            <p class="text-xs text-gray-400 mb-3">Satır, kolon ve modüller ekleyerek sayfanın görsel düzenini oluşturun.</p>
            @include('admin.pages._layout-builder')
            <div class="mt-4 rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-xs text-blue-800">
                <div class="font-semibold">Bu alan eski sistem içindir.</div>
                <p class="mt-1 leading-5">
                    <strong>Sayfa Düzeni (Layout Builder)</strong> eski Blade/CMS frontend’in <code>layout_json</code> yapısını düzenler.
                    Eğer sayfa yeni Next.js demo akışında <code>sections_json</code> kullanıyorsa, burada boş görünmesi normaldir.
                </p>
            </div>
        </div>

        @php
            $initialFrontendRegions = [];
            if (old('sections_json')) {
                $decodedOldSections = json_decode(old('sections_json'), true);
                $initialFrontendRegions = is_array($decodedOldSections)
                    ? \App\Support\FrontendSections::normalize($decodedOldSections)
                    : ($frontendRegions ?? \App\Support\FrontendSections::normalize($page->sections_json ?? []));
            } else {
                $initialFrontendRegions = $frontendRegions ?? \App\Support\FrontendSections::normalize($page->sections_json ?? []);
            }

            $availableFrontendTemplatesPayload = collect($availableFrontendSectionTemplates ?? [])
                ->map(fn ($template) => [
                    'id' => $template->id,
                    'name' => $template->name,
                    'type' => $template->type,
                    'variation' => $template->variation,
                    'render_mode' => $template->render_mode,
                    'component_key' => $template->component_key,
                    'schema' => $template->schema_json ?? [],
                    'default_content' => $template->default_content_json ?? [],
                ])
                ->values()
                ->all();

            $frontendSectionEditorPayload = [
                'initialRegions' => $initialFrontendRegions,
                'availableTemplates' => $availableFrontendTemplatesPayload,
            ];
        @endphp

        @if(isset($page) && ($page->site || !empty($initialFrontendRegions)))
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6"
                 x-data="frontendSectionEditor({{ \Illuminate\Support\Js::from($frontendSectionEditorPayload) }})">
                <div class="flex items-start justify-between gap-4 mb-4">
                    <div>
                        <h3 class="text-base font-semibold text-gray-800">
                            <i class="fas fa-layer-group mr-2 text-amber-500"></i>Frontend Section Mode İçeriği
                        </h3>
                        <p class="mt-1 text-xs text-gray-400">
                            Yeni editör `Header / Body / Footer` bölgeleri altında satır, kolon ve block mantığıyla çalışır.
                        </p>
                        <div class="mt-3 rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs text-amber-800">
                            <div class="font-semibold">Bu alan yeni sistem içindir.</div>
                            <p class="mt-1 leading-5">
                                <strong>Frontend Section Mode İçeriği</strong> Next.js frontend’in kullandığı <code>sections_json</code> yapısını düzenler.
                                Faz 2’de gelecek Structured Component Mode da aynı mantığın üstüne oturacak; sadece <code>render_mode</code> ve component çözümlemesi değişecek.
                            </p>
                        </div>
                        @if(isset($frontendRegions))
                            <div class="mt-3 rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-xs text-blue-800">
                                Geçiş katmanı aktif: mevcut block listesi arka planda <code>Header / Body / Footer</code> yapısına normalize ediliyor.
                                Sonraki adımda editör doğrudan bu region yapısını gösterecek.
                            </div>
                        @endif
                    </div>
                </div>

                <div class="space-y-6">
                    <template x-for="region in regionNames" :key="region">
                        <section class="rounded-2xl border border-gray-200 bg-gray-50/60 p-4">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-800" x-text="regionLabel(region)"></h4>
                                    <p class="mt-1 text-xs text-gray-500">
                                        Bölge içinde satır ekleyip kolonlar ve block’lar tanımlayabilirsin.
                                    </p>
                                </div>
                                <button type="button"
                                        @click="addRow(region)"
                                        class="inline-flex items-center gap-2 rounded-lg bg-amber-50 px-3 py-2 text-xs font-medium text-amber-700 hover:bg-amber-100">
                                    <i class="fas fa-plus"></i>
                                    Satır Ekle
                                </button>
                            </div>

                            <template x-if="(regions[region] || []).length === 0">
                                <div class="mt-4 rounded-xl border-2 border-dashed border-gray-300 bg-white px-4 py-8 text-center">
                                    <div class="text-sm font-medium text-gray-600">Henüz satır eklenmemiş</div>
                                    <p class="mt-1 text-xs text-gray-500" x-text="regionLabel(region) + ' alanı için ilk satırı ekleyebilirsin.'"></p>
                                </div>
                            </template>

                            <div class="mt-4 space-y-4">
                                <template x-for="(row, rowIndex) in (regions[region] || [])" :key="row._uid">
                                    <div class="rounded-xl border border-gray-200 bg-white p-4">
                                        <div class="flex items-start justify-between gap-4">
                                            <div>
                                                <div class="text-sm font-semibold text-gray-800" x-text="'Satır #' + (rowIndex + 1)"></div>
                                                <div class="mt-1 text-xs text-gray-500">Row id: <span x-text="row.id"></span></div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <label class="flex items-center gap-2 rounded-lg bg-gray-50 px-3 py-2 text-xs text-gray-700">
                                                    <input type="checkbox" x-model="row.is_active" class="h-4 w-4 rounded border-gray-300 text-amber-600 focus:ring-amber-500">
                                                    Satır aktif
                                                </label>
                                                <button type="button" @click="moveRow(region, rowIndex, -1)" class="rounded bg-gray-100 px-2 py-1 text-xs text-gray-600 hover:bg-gray-200">
                                                    <i class="fas fa-arrow-up"></i>
                                                </button>
                                                <button type="button" @click="moveRow(region, rowIndex, 1)" class="rounded bg-gray-100 px-2 py-1 text-xs text-gray-600 hover:bg-gray-200">
                                                    <i class="fas fa-arrow-down"></i>
                                                </button>
                                                <button type="button" @click="removeRow(region, rowIndex)" class="rounded bg-red-50 px-2 py-1 text-xs text-red-600 hover:bg-red-100">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="mt-4 flex items-center justify-between gap-4">
                                            <div class="text-xs text-gray-500">Kolonlar satır içinde block taşır. Genişliği 1-12 arasında ayarlayabilirsin.</div>
                                            <button type="button"
                                                    @click="addColumn(region, rowIndex)"
                                                    class="inline-flex items-center gap-2 rounded-lg bg-indigo-50 px-3 py-2 text-xs font-medium text-indigo-700 hover:bg-indigo-100">
                                                <i class="fas fa-columns"></i>
                                                Kolon Ekle
                                            </button>
                                        </div>

                                        <div class="mt-4 space-y-4">
                                            <template x-for="(column, columnIndex) in (row.columns || [])" :key="column._uid">
                                                <div class="rounded-xl border border-indigo-200 bg-indigo-50/40 p-4">
                                                    <div class="flex items-start justify-between gap-4">
                                                        <div class="flex-1">
                                                            <div class="text-sm font-semibold text-indigo-900" x-text="'Kolon #' + (columnIndex + 1)"></div>
                                                            <div class="mt-3 grid gap-3 sm:grid-cols-2">
                                                                <div>
                                                                    <label class="mb-1 block text-xs font-medium text-indigo-800">Genişlik</label>
                                                                    <input type="number"
                                                                           min="1"
                                                                           max="12"
                                                                           x-model.number="column.width"
                                                                           class="w-full rounded-lg border border-indigo-200 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                                                                </div>
                                                                <label class="mt-6 flex items-center gap-2 rounded-lg bg-white px-3 py-2 text-sm text-indigo-900">
                                                                    <input type="checkbox" x-model="column.is_active" class="h-4 w-4 rounded border-indigo-300 text-indigo-600 focus:ring-indigo-500">
                                                                    Kolon aktif
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="flex items-center gap-2">
                                                            <button type="button" @click="moveColumn(region, rowIndex, columnIndex, -1)" class="rounded bg-white px-2 py-1 text-xs text-indigo-600 hover:bg-indigo-100">
                                                                <i class="fas fa-arrow-left"></i>
                                                            </button>
                                                            <button type="button" @click="moveColumn(region, rowIndex, columnIndex, 1)" class="rounded bg-white px-2 py-1 text-xs text-indigo-600 hover:bg-indigo-100">
                                                                <i class="fas fa-arrow-right"></i>
                                                            </button>
                                                            <button type="button" @click="removeColumn(region, rowIndex, columnIndex)" class="rounded bg-red-50 px-2 py-1 text-xs text-red-600 hover:bg-red-100">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>

                                                    <div class="mt-4 flex flex-wrap items-center gap-2">
                                                        <select x-model="column.newTemplateId"
                                                                class="rounded-lg border border-indigo-200 bg-white px-3 py-2 text-xs focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                                                            <option value="">Block şablonu seç</option>
                                                            @foreach(($availableFrontendSectionTemplates ?? []) as $templateOption)
                                                                <option value="{{ $templateOption->id }}">{{ $templateOption->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <button type="button"
                                                                @click="addBlock(region, rowIndex, columnIndex)"
                                                                class="inline-flex items-center gap-2 rounded-lg bg-white px-3 py-2 text-xs font-medium text-indigo-700 hover:bg-indigo-100">
                                                            <i class="fas fa-plus"></i>
                                                            Block Ekle
                                                        </button>
                                                    </div>

                                                    <div class="mt-4 space-y-4">
                                                        <template x-if="(column.blocks || []).length === 0">
                                                            <div class="rounded-xl border-2 border-dashed border-indigo-200 bg-white px-4 py-6 text-center text-xs text-indigo-700">
                                                                Bu kolonda henüz block yok.
                                                            </div>
                                                        </template>

                                                        <template x-for="(block, blockIndex) in (column.blocks || [])" :key="block._uid">
                                                            <div class="rounded-xl border border-gray-200 bg-white p-4">
                                                                <div class="flex items-start justify-between gap-4">
                                                                    <div>
                                                                        <div class="text-sm font-semibold text-gray-800" x-text="block.template_name || block.type || 'Block'"></div>
                                                                        <div class="mt-1 text-xs text-gray-500">
                                                                            <span x-text="'type: ' + (block.type || '-')"></span>
                                                                            <span> | </span>
                                                                            <span x-text="'variation: ' + (block.variation || '-')"></span>
                                                                            <span> | </span>
                                                                            <span x-text="'render: ' + (block.render_mode || '-')"></span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="flex items-center gap-2">
                                                                        <label class="flex items-center gap-2 rounded-lg bg-gray-50 px-3 py-2 text-xs text-gray-700">
                                                                            <input type="checkbox" x-model="block.is_active" class="h-4 w-4 rounded border-gray-300 text-amber-600 focus:ring-amber-500">
                                                                            Block aktif
                                                                        </label>
                                                                        <button type="button" @click="moveBlock(region, rowIndex, columnIndex, blockIndex, -1)" class="rounded bg-gray-100 px-2 py-1 text-xs text-gray-600 hover:bg-gray-200">
                                                                            <i class="fas fa-arrow-up"></i>
                                                                        </button>
                                                                        <button type="button" @click="moveBlock(region, rowIndex, columnIndex, blockIndex, 1)" class="rounded bg-gray-100 px-2 py-1 text-xs text-gray-600 hover:bg-gray-200">
                                                                            <i class="fas fa-arrow-down"></i>
                                                                        </button>
                                                                        <button type="button" @click="removeBlock(region, rowIndex, columnIndex, blockIndex)" class="rounded bg-red-50 px-2 py-1 text-xs text-red-600 hover:bg-red-100">
                                                                            <i class="fas fa-trash"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>

                                                                <div class="mt-4 grid gap-3">
                                                                    <template x-for="[fieldName, fieldSchema] in Object.entries(block.schema || {})" :key="fieldName">
                                                                        <div>
                                                                            <label class="mb-1 block text-xs font-medium text-gray-600" x-text="fieldName"></label>

                                                                            <template x-if="(fieldSchema.type || 'text') === 'textarea'">
                                                                                <textarea x-model="block.content[fieldName]"
                                                                                          rows="4"
                                                                                          class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200"></textarea>
                                                                            </template>

                                                                            <template x-if="(fieldSchema.type || 'text') === 'number'">
                                                                                <input type="number"
                                                                                       x-model="block.content[fieldName]"
                                                                                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200">
                                                                            </template>

                                                                            <template x-if="(fieldSchema.type || 'text') === 'boolean'">
                                                                                <label class="flex items-center gap-2 rounded-lg bg-gray-50 px-3 py-2 text-sm text-gray-700">
                                                                                    <input type="checkbox" x-model="block.content[fieldName]" class="h-4 w-4 rounded border-gray-300 text-amber-600 focus:ring-amber-500">
                                                                                    true / false
                                                                                </label>
                                                                            </template>

                                                                            <template x-if="!['textarea', 'number', 'boolean'].includes(fieldSchema.type || 'text')">
                                                                                <input type="text"
                                                                                       x-model="block.content[fieldName]"
                                                                                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200">
                                                                            </template>
                                                                        </div>
                                                                    </template>
                                                                </div>

                                                                <div x-show="block.type === 'article-list'" class="mt-4 rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs text-amber-800">
                                                                    Bu block örnek olarak site içindeki blog yazılarından kart üretir. Yani içerik doğrudan <code>page->articles()</code> ilişkisine bağlı değildir.
                                                                </div>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </section>
                    </template>
                </div>

                <input type="hidden" name="sections_json" :value="serializedRegions">

                <div x-data="{ openFrontendJson: false }" class="mt-5 space-y-3">
                    <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-xs text-amber-800">
                        Bu alan gelişmiş/teknik kullanım içindir. Normal kullanımda yukarıdaki kart editörü yeterli olmalı.
                    </div>
                    <button type="button"
                            @click="openFrontendJson = !openFrontendJson"
                            class="inline-flex items-center gap-2 rounded-lg bg-gray-100 px-3 py-2 text-xs font-medium text-gray-700 hover:bg-gray-200">
                        <i class="fas fa-code"></i>
                        <span x-text="openFrontendJson ? 'Ham JSON Gizle' : 'Ham JSON Göster'"></span>
                    </button>
                    <div>
                        <label for="sections_json_preview" class="block text-sm font-medium text-gray-700 mb-2">sections_json önizleme</label>
                        <textarea id="sections_json_preview"
                                  x-show="openFrontendJson"
                                  x-model="serializedRegions"
                                  rows="18"
                                  class="w-full rounded-lg border border-gray-300 px-3 py-2 font-mono text-xs focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200"></textarea>
                    </div>
                </div>
            </div>
        @endif

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

@push('scripts')
<script>
function frontendSectionEditor({ initialRegions = null, availableTemplates = [] }) {
    return {
        regions: { header: [], body: [], footer: [] },
        regionNames: ['header', 'body', 'footer'],
        availableTemplates,

        init() {
            this.regions = this.normalizeRegions(initialRegions);
            this.normalizeSortOrder();
        },

        get serializedRegions() {
            return JSON.stringify({
                version: 2,
                regions: this.serializeRegions(),
            }, null, 2);
        },

        getTemplateById(templateId) {
            return this.availableTemplates.find((template) => String(template.id) === String(templateId));
        },

        regionLabel(region) {
            return {
                header: 'Header',
                body: 'Body',
                footer: 'Footer',
            }[region] || region;
        },

        normalizeRegions(initialRegions) {
            const output = {
                header: [],
                body: [],
                footer: [],
            };

            const sourceRegions = initialRegions?.regions || output;

            Object.keys(output).forEach((region) => {
                output[region] = (sourceRegions[region] || []).map((row, rowIndex) => ({
                    _uid: row._uid || this.generateUid('row'),
                    id: row.id || `row_${region}_${rowIndex + 1}`,
                    type: 'row',
                    is_active: row.is_active !== false,
                    columns: (row.columns || []).map((column, columnIndex) => ({
                        _uid: column._uid || this.generateUid('column'),
                        id: column.id || `col_${region}_${rowIndex + 1}_${columnIndex + 1}`,
                        width: Number(column.width || 12),
                        is_active: column.is_active !== false,
                        newTemplateId: '',
                        blocks: (column.blocks || []).map((block, blockIndex) => this.hydrateBlock(block, region, rowIndex, columnIndex, blockIndex)),
                    })),
                }));
            });

            return output;
        },

        hydrateBlock(block, region, rowIndex, columnIndex, blockIndex) {
            const template = this.getTemplateById(block.section_template_id);

            return {
                _uid: block._uid || this.generateUid('block'),
                id: block.id || `block_${block.type || 'item'}_${rowIndex + 1}_${columnIndex + 1}_${blockIndex + 1}`,
                type: block.type || template?.type || '',
                variation: block.variation || template?.variation || 'default',
                render_mode: block.render_mode || template?.render_mode || 'html',
                section_template_id: block.section_template_id || template?.id || null,
                template_name: block.template_name || template?.name || '',
                component_key: block.component_key || template?.component_key || null,
                schema: block.schema || template?.schema || {},
                content: JSON.parse(JSON.stringify(block.content || template?.default_content || {})),
                is_active: block.is_active !== false,
                sort_order: block.sort_order || (blockIndex + 1),
            };
        },

        generateUid(prefix) {
            return `${prefix}_${Date.now()}_${Math.random().toString(36).slice(2, 8)}`;
        },

        createRow(region, rowIndex) {
            return {
                _uid: this.generateUid('row'),
                id: `row_${region}_${rowIndex + 1}`,
                type: 'row',
                is_active: true,
                columns: [this.createColumn(region, rowIndex, 0)],
            };
        },

        createColumn(region, rowIndex, columnIndex) {
            return {
                _uid: this.generateUid('column'),
                id: `col_${region}_${rowIndex + 1}_${columnIndex + 1}`,
                width: 12,
                is_active: true,
                newTemplateId: '',
                blocks: [],
            };
        },

        addRow(region) {
            this.regions[region].push(this.createRow(region, this.regions[region].length));
            this.normalizeSortOrder();
        },

        removeRow(region, rowIndex) {
            this.regions[region].splice(rowIndex, 1);
            this.normalizeSortOrder();
        },

        moveRow(region, rowIndex, direction) {
            const targetIndex = rowIndex + direction;
            if (targetIndex < 0 || targetIndex >= this.regions[region].length) return;
            [this.regions[region][rowIndex], this.regions[region][targetIndex]] = [this.regions[region][targetIndex], this.regions[region][rowIndex]];
            this.normalizeSortOrder();
        },

        addColumn(region, rowIndex) {
            const row = this.regions[region][rowIndex];
            row.columns.push(this.createColumn(region, rowIndex, row.columns.length));
            this.normalizeSortOrder();
        },

        removeColumn(region, rowIndex, columnIndex) {
            this.regions[region][rowIndex].columns.splice(columnIndex, 1);
            this.normalizeSortOrder();
        },

        moveColumn(region, rowIndex, columnIndex, direction) {
            const columns = this.regions[region][rowIndex].columns;
            const targetIndex = columnIndex + direction;
            if (targetIndex < 0 || targetIndex >= columns.length) return;
            [columns[columnIndex], columns[targetIndex]] = [columns[targetIndex], columns[columnIndex]];
            this.normalizeSortOrder();
        },

        addBlock(region, rowIndex, columnIndex) {
            const column = this.regions[region][rowIndex].columns[columnIndex];
            const template = this.getTemplateById(column.newTemplateId);
            if (!template) return;

            column.blocks.push(this.hydrateBlock({
                id: `${template.type}_${column.blocks.length + 1}`,
                type: template.type,
                variation: template.variation,
                render_mode: template.render_mode,
                section_template_id: template.id,
                template_name: template.name,
                component_key: template.component_key || null,
                schema: template.schema || {},
                content: JSON.parse(JSON.stringify(template.default_content || {})),
                is_active: true,
            }, region, rowIndex, columnIndex, column.blocks.length));

            column.newTemplateId = '';
            this.normalizeSortOrder();
        },

        removeBlock(region, rowIndex, columnIndex, blockIndex) {
            this.regions[region][rowIndex].columns[columnIndex].blocks.splice(blockIndex, 1);
            this.normalizeSortOrder();
        },

        moveBlock(region, rowIndex, columnIndex, blockIndex, direction) {
            const blocks = this.regions[region][rowIndex].columns[columnIndex].blocks;
            const targetIndex = blockIndex + direction;
            if (targetIndex < 0 || targetIndex >= blocks.length) return;
            [blocks[blockIndex], blocks[targetIndex]] = [blocks[targetIndex], blocks[blockIndex]];
            this.normalizeSortOrder();
        },

        serializeRegions() {
            const output = {
                header: [],
                body: [],
                footer: [],
            };

            Object.keys(output).forEach((region) => {
                output[region] = (this.regions[region] || []).map((row, rowIndex) => ({
                    id: row.id || `row_${region}_${rowIndex + 1}`,
                    type: 'row',
                    is_active: row.is_active !== false,
                    columns: (row.columns || []).map((column, columnIndex) => ({
                        id: column.id || `col_${region}_${rowIndex + 1}_${columnIndex + 1}`,
                        width: Number(column.width || 12),
                        is_active: column.is_active !== false,
                        blocks: (column.blocks || []).map((block, blockIndex) => ({
                            id: block.id || `block_${block.type || 'item'}_${blockIndex + 1}`,
                            type: block.type,
                            variation: block.variation,
                            render_mode: block.render_mode,
                            section_template_id: block.section_template_id,
                            component_key: block.component_key,
                            is_active: block.is_active !== false,
                            sort_order: block.sort_order || (blockIndex + 1),
                            content: block.content || {},
                        })),
                    })),
                }));
            });

            return output;
        },

        normalizeSortOrder() {
            this.regionNames.forEach((region) => {
                (this.regions[region] || []).forEach((row, rowIndex) => {
                    row.id = row.id || `row_${region}_${rowIndex + 1}`;

                    (row.columns || []).forEach((column, columnIndex) => {
                        column.id = column.id || `col_${region}_${rowIndex + 1}_${columnIndex + 1}`;
                        column.width = Math.min(12, Math.max(1, Number(column.width || 12)));

                        (column.blocks || []).forEach((block, blockIndex) => {
                            block.sort_order = blockIndex + 1;
                        });
                    });
                });
            });
        },
    };
}
</script>
@endpush
