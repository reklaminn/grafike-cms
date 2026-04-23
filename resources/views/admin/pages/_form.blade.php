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

        @php
            $preferredBuilder = !empty(old('sections_json')) || (!old('layout_json') && isset($page) && !empty($page->sections_json))
                ? 'frontend'
                : 'legacy';
        @endphp

        <div x-data="{ builderMode: '{{ $preferredBuilder }}' }" class="space-y-6">
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h3 class="text-base font-semibold text-gray-800">Sayfa Düzeni Editörü</h3>
                    <p class="mt-1 text-xs text-gray-500">Bu sayfayı eski Blade builder veya yeni Next.js section editor ile düzenleyebilirsin.</p>
                </div>
                <div class="inline-flex rounded-xl bg-gray-100 p-1">
                    <button type="button"
                            @click="builderMode = 'frontend'"
                            class="rounded-lg px-3 py-2 text-xs font-medium transition-colors"
                            :class="builderMode === 'frontend' ? 'bg-white text-amber-700 shadow-sm' : 'text-gray-600 hover:text-gray-800'">
                        Yeni Builder
                    </button>
                    <button type="button"
                            @click="builderMode = 'legacy'"
                            class="rounded-lg px-3 py-2 text-xs font-medium transition-colors"
                            :class="builderMode === 'legacy' ? 'bg-white text-indigo-700 shadow-sm' : 'text-gray-600 hover:text-gray-800'">
                        Eski Builder
                    </button>
                </div>
            </div>
        </div>

        <!-- Visual Layout Builder -->
        <div x-show="builderMode === 'legacy'" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
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
                    'html_template' => $template->html_template,
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
            <div x-show="builderMode === 'frontend'" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6"
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

                <div class="flex items-center justify-between flex-wrap gap-3 mb-4">
                    <div class="flex items-center gap-2">
                        <button type="button" @click="addRow('header')"
                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-50 text-blue-700 text-xs font-medium rounded-lg hover:bg-blue-100 transition-colors">
                            <i class="fas fa-plus"></i> Header Satır
                        </button>
                        <button type="button" @click="addRow('body')"
                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-50 text-green-700 text-xs font-medium rounded-lg hover:bg-green-100 transition-colors">
                            <i class="fas fa-plus"></i> Body Satır
                        </button>
                        <button type="button" @click="addRow('footer')"
                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-purple-50 text-purple-700 text-xs font-medium rounded-lg hover:bg-purple-100 transition-colors">
                            <i class="fas fa-plus"></i> Footer Satır
                        </button>
                    </div>
                    <div class="text-xs text-gray-400">
                        Yeni builder, eski alanın kullanım diline yakınlaştırıldı.
                    </div>
                </div>

                <div class="space-y-6">
                    <template x-for="region in regionNames" :key="region">
                        <section class="rounded-2xl border p-4"
                                 :class="regionShellClass(region)">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-bold uppercase px-2 py-0.5 rounded"
                                              :class="regionBadgeClass(region)"
                                              x-text="regionLabel(region)"></span>
                                        <h4 class="text-sm font-semibold text-gray-800">bölgesi</h4>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">
                                        Bölge içinde satır ekleyip kolonlar ve block’lar tanımlayabilirsin.
                                    </p>
                                </div>
                                <div class="flex flex-col items-end gap-2">
                                    <button type="button"
                                            @click="addRow(region)"
                                            class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-xs font-medium"
                                            :class="regionButtonClass(region)">
                                        <i class="fas fa-plus"></i>
                                        Satır Ekle
                                    </button>
                                    <div class="flex flex-wrap justify-end gap-2">
                                        <template x-for="preset in getRegionPresets(region)" :key="preset.key">
                                            <button type="button"
                                                    @click="applyPreset(region, preset.key)"
                                                    class="inline-flex items-center gap-1 rounded-lg bg-white px-2.5 py-1.5 text-[11px] font-medium text-gray-700 border border-gray-200 hover:border-amber-300 hover:bg-amber-50 hover:text-amber-700 transition-colors">
                                                <i class="fas fa-wand-magic-sparkles text-[10px]"></i>
                                                <span x-text="preset.label"></span>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <template x-if="(regions[region] || []).length === 0">
                                <div class="mt-4 rounded-xl border-2 border-dashed border-gray-300 bg-white px-4 py-8 text-center">
                                    <div class="text-sm font-medium text-gray-600">Henüz satır eklenmemiş</div>
                                    <p class="mt-1 text-xs text-gray-500" x-text="regionLabel(region) + ' alanı için ilk satırı ekleyebilirsin.'"></p>
                                </div>
                            </template>

                            <div class="mt-4 space-y-4">
                                <template x-for="(row, rowIndex) in (regions[region] || [])" :key="row._uid">
                                    <div class="rounded-xl border bg-white overflow-hidden"
                                         :class="rowShellClass(region)">
                                        <div class="flex items-start justify-between gap-4">
                                            <div class="px-4 py-3">
                                                <div class="flex items-center gap-2">
                                                    <i class="fas fa-grip-vertical text-gray-400"></i>
                                                    <span class="text-xs font-bold uppercase px-2 py-0.5 rounded"
                                                          :class="regionBadgeClass(region)"
                                                          x-text="regionLabel(region)"></span>
                                                    <div class="text-sm font-semibold text-gray-800" x-text="'Satır #' + (rowIndex + 1)"></div>
                                                </div>
                                                <div class="mt-1 text-xs text-gray-500">Row id: <span x-text="row.id"></span></div>
                                            </div>
                                            <div class="flex items-center gap-2 px-4 py-3">
                                                <label class="flex items-center gap-2 rounded-lg bg-gray-50 px-3 py-2 text-xs text-gray-700">
                                                    <input type="checkbox" x-model="row.is_active" class="h-4 w-4 rounded border-gray-300 text-amber-600 focus:ring-amber-500">
                                                    Satır aktif
                                                </label>
                                                <button type="button" @click="openRowSettings(region, rowIndex)" class="rounded bg-gray-100 px-2 py-1 text-xs text-gray-600 hover:bg-gray-200">
                                                    <i class="fas fa-cog"></i>
                                                </button>
                                                <button type="button" @click="toggleRowExpand(region, rowIndex)" class="rounded bg-gray-100 px-2 py-1 text-xs text-gray-600 hover:bg-gray-200">
                                                    <i class="fas" :class="row._expanded === false ? 'fa-chevron-down' : 'fa-chevron-up'"></i>
                                                </button>
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

                                        <div x-show="row._expanded !== false" class="mt-4 px-4 pb-4 flex items-center justify-between gap-4">
                                            <div class="space-y-2">
                                                <div class="flex flex-wrap gap-2">
                                                    <button type="button"
                                                            @click="applyColumnPreset(region, rowIndex, [12])"
                                                            class="inline-flex items-center gap-1 rounded-lg bg-gray-100 px-2.5 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-200">
                                                        1 Kolon
                                                    </button>
                                                    <button type="button"
                                                            @click="applyColumnPreset(region, rowIndex, [6, 6])"
                                                            class="inline-flex items-center gap-1 rounded-lg bg-gray-100 px-2.5 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-200">
                                                        2 Kolon
                                                    </button>
                                                    <button type="button"
                                                            @click="applyColumnPreset(region, rowIndex, [4, 4, 4])"
                                                            class="inline-flex items-center gap-1 rounded-lg bg-gray-100 px-2.5 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-200">
                                                        3 Kolon
                                                    </button>
                                                </div>
                                            </div>
                                            <button type="button"
                                                    @click="addColumn(region, rowIndex)"
                                                    class="inline-flex items-center gap-2 rounded-lg bg-indigo-50 px-3 py-2 text-xs font-medium text-indigo-700 hover:bg-indigo-100">
                                                <i class="fas fa-columns"></i>
                                                Kolon Ekle
                                            </button>
                                        </div>

                                        <div x-show="row._expanded !== false" class="mt-4 grid grid-cols-12 gap-4 px-4 pb-4">
                                            <template x-for="(column, columnIndex) in (row.columns || [])" :key="column._uid">
                                                <div class="rounded-2xl border border-gray-200 bg-white overflow-hidden"
                                                     :style="editorColumnCanvasStyle(column)">
                                                    <div class="flex items-start justify-between gap-4 border-b border-gray-100 bg-gray-50 px-4 py-3">
                                                        <div class="flex-1">
                                                            <div class="flex items-center gap-2">
                                                                <i class="fas fa-grip-vertical text-gray-300"></i>
                                                                <span class="text-sm font-semibold text-gray-800" x-text="columnClassLabel(column)"></span>
                                                                <span class="rounded bg-indigo-100 px-2 py-0.5 text-[11px] font-semibold text-indigo-800"
                                                                      x-text="columnClassLabel(column)"></span>
                                                                <span x-show="column.is_active === false" class="text-[10px] font-semibold uppercase tracking-wider text-red-500">Pasif</span>
                                                            </div>
                                                            <div class="mt-1 text-[11px] text-gray-500">
                                                                <span x-text="'Kolon #' + (columnIndex + 1)"></span>
                                                            </div>
                                                        </div>
                                                        <div class="flex items-center gap-2">
                                                            <button type="button" @click="openColumnSettings(region, rowIndex, columnIndex)" class="rounded bg-white px-2 py-1 text-xs text-indigo-700 hover:bg-indigo-100">
                                                                <i class="fas fa-cog"></i>
                                                            </button>
                                                            <button type="button"
                                                                    @click="moveColumn(region, rowIndex, columnIndex, -1)"
                                                                    :disabled="!canMoveColumn(row, columnIndex, -1)"
                                                                    :title="canMoveColumn(row, columnIndex, -1) ? 'Kolonu sola taşı' : 'Bu kolon sola taşınamaz'"
                                                                    class="rounded px-2 py-1 text-xs"
                                                                    :class="canMoveColumn(row, columnIndex, -1) ? 'bg-white text-indigo-600 hover:bg-indigo-100' : 'bg-gray-100 text-gray-300 cursor-not-allowed'">
                                                                <i class="fas fa-arrow-left"></i>
                                                            </button>
                                                            <button type="button"
                                                                    @click="moveColumn(region, rowIndex, columnIndex, 1)"
                                                                    :disabled="!canMoveColumn(row, columnIndex, 1)"
                                                                    :title="canMoveColumn(row, columnIndex, 1) ? 'Kolonu sağa taşı' : 'Bu kolon sağa taşınamaz'"
                                                                    class="rounded px-2 py-1 text-xs"
                                                                    :class="canMoveColumn(row, columnIndex, 1) ? 'bg-white text-indigo-600 hover:bg-indigo-100' : 'bg-gray-100 text-gray-300 cursor-not-allowed'">
                                                                <i class="fas fa-arrow-right"></i>
                                                            </button>
                                                            <button type="button" @click="removeColumn(region, rowIndex, columnIndex)" class="rounded bg-red-50 px-2 py-1 text-xs text-red-600 hover:bg-red-100">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>

                                                    <div class="p-4 space-y-4">
                                                        <template x-if="(column.blocks || []).length === 0">
                                                            <div class="rounded-xl border-2 border-dashed border-gray-200 bg-gray-50 px-4 py-6 text-center text-xs text-gray-500">
                                                                Bu kolonda henüz block yok.
                                                            </div>
                                                        </template>

                                                        <template x-for="(block, blockIndex) in (column.blocks || [])" :key="block._uid">
                                                            <div class="rounded-xl border border-indigo-200 bg-white p-4">
                                                                <div class="flex items-start justify-between gap-4">
                                                                    <div>
                                                                        <div class="flex items-center gap-2">
                                                                            <i class="fas fa-grip-vertical text-gray-300"></i>
                                                                            <div class="text-sm font-semibold text-gray-800" x-text="block.template_name || block.type || 'Block'"></div>
                                                                        </div>
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
                                                                        <button type="button" @click="openBlockSettings(region, rowIndex, columnIndex, blockIndex)" class="rounded bg-indigo-50 px-2 py-1 text-xs text-indigo-700 hover:bg-indigo-100">
                                                                            <i class="fas fa-cog"></i>
                                                                        </button>
                                                                        <button type="button" @click="duplicateBlock(region, rowIndex, columnIndex, blockIndex)" class="rounded bg-gray-100 px-2 py-1 text-xs text-gray-600 hover:bg-gray-200" title="Block çoğalt">
                                                                            <i class="fas fa-copy"></i>
                                                                        </button>
                                                                        <button type="button" @click="removeBlock(region, rowIndex, columnIndex, blockIndex)" class="rounded bg-red-50 px-2 py-1 text-xs text-red-600 hover:bg-red-100">
                                                                            <i class="fas fa-trash"></i>
                                                                        </button>
                                                                        <div class="relative">
                                                                            <button type="button"
                                                                                    @click="openBlockMenuFor = openBlockMenuFor === block._uid ? null : block._uid"
                                                                                    class="rounded bg-gray-100 px-2 py-1 text-xs text-gray-600 hover:bg-gray-200"
                                                                                    title="Diğer işlemler">
                                                                                <i class="fas fa-ellipsis-h"></i>
                                                                            </button>
                                                                            <div x-show="openBlockMenuFor === block._uid"
                                                                                 x-cloak
                                                                                 @click.outside="openBlockMenuFor = null"
                                                                                 class="absolute right-0 top-9 z-10 w-40 rounded-xl border border-gray-200 bg-white p-2 shadow-lg">
                                                                                <button type="button"
                                                                                        @click="moveBlock(region, rowIndex, columnIndex, blockIndex, -1); openBlockMenuFor = null"
                                                                                        class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-left text-xs text-gray-700 hover:bg-gray-50">
                                                                                    <i class="fas fa-arrow-up w-3"></i>
                                                                                    Yukarı taşı
                                                                                </button>
                                                                                <button type="button"
                                                                                        @click="moveBlock(region, rowIndex, columnIndex, blockIndex, 1); openBlockMenuFor = null"
                                                                                        class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-left text-xs text-gray-700 hover:bg-gray-50">
                                                                                    <i class="fas fa-arrow-down w-3"></i>
                                                                                    Aşağı taşı
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="mt-3 rounded-lg border border-gray-100 bg-gray-50 px-3 py-2 text-xs text-gray-600">
                                                                    <span class="font-medium text-gray-700">Özet:</span>
                                                                    <span x-text="blockSummary(block)"></span>
                                                                </div>

                                                                <div x-show="block.type === 'article-list'" class="mt-4 rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs text-amber-800">
                                                                    Bu block örnek olarak site içindeki blog yazılarından kart üretir. Yani içerik doğrudan <code>page->articles()</code> ilişkisine bağlı değildir.
                                                                </div>
                                                            </div>
                                                        </template>

                                                        <button type="button"
                                                                @click="openBlockPicker(region, rowIndex, columnIndex)"
                                                                class="flex w-full items-center justify-center gap-2 rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 px-4 py-5 text-sm font-medium text-gray-500 hover:border-indigo-300 hover:bg-indigo-50 hover:text-indigo-700 transition-colors">
                                                            <i class="fas fa-plus"></i>
                                                            Block Ekle
                                                        </button>
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

                <div x-show="pickerModalOpen" x-cloak
                     class="fixed inset-0 z-[70] flex items-center justify-center bg-black/50 p-4"
                     @click.self="closeBlockPicker()">
                    <div class="w-full max-w-3xl rounded-2xl bg-white shadow-2xl border border-gray-200 p-5">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <h4 class="text-base font-semibold text-gray-900">Block Şablonları</h4>
                                <p class="mt-1 text-xs text-gray-500">Kolona eklenecek block'u seç. Aynı block’tan tekrar eklemek için aynı şablonu yeniden seçebilirsin.</p>
                            </div>
                            <button type="button"
                                    @click="closeBlockPicker()"
                                    class="rounded-lg bg-gray-100 px-3 py-2 text-xs text-gray-600 hover:bg-gray-200">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <div class="mt-4">
                            <input type="text"
                                   x-model="pickerSearch"
                                   placeholder="Block ara..."
                                   class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                        </div>

                        <div class="mt-4 grid gap-3 md:grid-cols-2">
                            <template x-for="template in getFilteredTemplates(pickerSearch)" :key="template.id">
                                <button type="button"
                                        @click="pickTemplate(template.id)"
                                        class="rounded-xl border border-gray-200 bg-gray-50 p-4 text-left hover:border-indigo-300 hover:bg-indigo-50 transition-colors">
                                    <div class="flex items-center justify-between gap-2">
                                        <div class="text-sm font-semibold text-gray-800" x-text="template.name"></div>
                                        <i class="fas fa-plus text-indigo-500"></i>
                                    </div>
                                    <div class="mt-1 text-xs text-gray-500">
                                        <span x-text="template.type"></span>
                                        <span> • </span>
                                        <span x-text="template.variation"></span>
                                    </div>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                <div x-show="settingsModalOpen" x-cloak
                     class="fixed inset-0 z-[70] flex items-center justify-center bg-black/50 p-4"
                     @click.self="closeBlockSettings()">
                    <div class="w-full max-w-3xl rounded-2xl bg-white shadow-2xl border border-gray-200 p-5 max-h-[85vh] overflow-y-auto">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <h4 class="text-base font-semibold text-gray-900">Block Ayarları</h4>
                                <p class="mt-1 text-xs text-gray-500" x-text="settingsBlock ? (settingsBlock.template_name || settingsBlock.type) : ''"></p>
                            </div>
                            <button type="button"
                                    @click="closeBlockSettings()"
                                    class="rounded-lg bg-gray-100 px-3 py-2 text-xs text-gray-600 hover:bg-gray-200">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <template x-if="settingsBlock">
                            <div class="mt-5 space-y-4">
                                <div class="flex flex-wrap gap-2 border-b border-gray-200 pb-3">
                                    <button type="button" @click="settingsTab = 'content'" class="rounded-lg px-3 py-1.5 text-xs font-medium"
                                            :class="settingsTab === 'content' ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'">
                                        İçerik
                                    </button>
                                    <button type="button" @click="settingsTab = 'style'" class="rounded-lg px-3 py-1.5 text-xs font-medium"
                                            :class="settingsTab === 'style' ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'">
                                        Stil
                                    </button>
                                    <button type="button" @click="settingsTab = 'code'" class="rounded-lg px-3 py-1.5 text-xs font-medium"
                                            :class="settingsTab === 'code' ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'">
                                        Kod
                                    </button>
                                </div>

                                <div class="grid gap-4 sm:grid-cols-2">
                                    <label class="flex items-center gap-2 rounded-lg bg-gray-50 px-3 py-2 text-sm text-gray-700">
                                        <input type="checkbox" x-model="settingsBlock.is_active" class="h-4 w-4 rounded border-gray-300 text-amber-600 focus:ring-amber-500">
                                        Block aktif
                                    </label>
                                    <div class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-xs text-gray-600">
                                        <span class="font-medium text-gray-700">Özet:</span>
                                        <span x-text="blockSummary(settingsBlock)"></span>
                                    </div>
                                </div>

                                <div x-show="settingsTab === 'content'" class="grid gap-3">
                                    <template x-for="[fieldName, fieldSchema] in Object.entries(settingsBlock.schema || {})" :key="fieldName">
                                        <div>
                                            <label class="mb-1 block text-xs font-medium text-gray-600" x-text="fieldName"></label>

                                            <template x-if="(fieldSchema.type || 'text') === 'textarea'">
                                                <textarea x-model="settingsBlock.content[fieldName]"
                                                          rows="4"
                                                          class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200"></textarea>
                                            </template>

                                            <template x-if="(fieldSchema.type || 'text') === 'number'">
                                                <input type="number"
                                                       x-model="settingsBlock.content[fieldName]"
                                                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200">
                                            </template>

                                            <template x-if="(fieldSchema.type || 'text') === 'boolean'">
                                                <label class="flex items-center gap-2 rounded-lg bg-gray-50 px-3 py-2 text-sm text-gray-700">
                                                    <input type="checkbox" x-model="settingsBlock.content[fieldName]" class="h-4 w-4 rounded border-gray-300 text-amber-600 focus:ring-amber-500">
                                                    true / false
                                                </label>
                                            </template>

                                            <template x-if="!['textarea', 'number', 'boolean'].includes(fieldSchema.type || 'text')">
                                                <input type="text"
                                                       x-model="settingsBlock.content[fieldName]"
                                                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200">
                                            </template>
                                        </div>
                                    </template>
                                </div>

                                <div x-show="settingsTab === 'style'" class="grid gap-4 sm:grid-cols-2">
                                    <div>
                                        <label class="mb-1 block text-xs font-medium text-gray-600">Wrapper Tag</label>
                                        <input type="text" x-model="settingsBlock.wrapper_tag"
                                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200">
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-xs font-medium text-gray-600">CSS Sınıf</label>
                                        <input type="text" x-model="settingsBlock.css_class"
                                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200">
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-xs font-medium text-gray-600">Element ID</label>
                                        <input type="text" x-model="settingsBlock.element_id"
                                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200">
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-xs font-medium text-gray-600">Inline Style</label>
                                        <input type="text" x-model="settingsBlock.inline_style"
                                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200">
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="mb-1 block text-xs font-medium text-gray-600">Diğer Attributes</label>
                                        <input type="text" x-model="settingsBlock.custom_attributes"
                                               placeholder='data-animate="fade-up" aria-label="..."'
                                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200">
                                    </div>
                                </div>

                                <div x-show="settingsTab === 'code'" class="space-y-4">
                                    <div>
                                        <label class="mb-1 block text-xs font-medium text-gray-600">Render Önizleme</label>
                                        <div class="rounded-lg border border-gray-200 bg-white p-3 max-h-56 overflow-auto">
                                            <div class="origin-top-left scale-[0.82] transform-gpu rounded-lg border border-dashed border-gray-200 p-3"
                                                 style="width:122%;"
                                                 x-html="blockRenderedHtml(settingsBlock)"></div>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-xs font-medium text-gray-600">Üretilen HTML Kodu</label>
                                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-3 text-xs text-gray-700 font-mono overflow-x-auto"
                                             x-text="blockCodePreview(settingsBlock)"></div>
                                    </div>
                                    <div x-show="settingsBlock.render_mode === 'html'">
                                        <label class="mb-1 block text-xs font-medium text-gray-600">HTML Override</label>
                                        <textarea x-model="settingsBlock.html_override"
                                                  rows="8"
                                                  placeholder="Boş bırakırsan şablonun varsayılan HTML'i kullanılır."
                                                  class="w-full rounded-lg border border-gray-300 px-3 py-2 font-mono text-xs focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200"></textarea>
                                    </div>
                                    <div x-show="settingsBlock.render_mode !== 'html'" class="rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-xs text-blue-800">
                                        Component mode için ham HTML override yerine props ve stil alanları kullanılmalı.
                                    </div>
                                </div>

                                <div class="flex items-center justify-end gap-3 border-t border-gray-200 pt-4">
                                    <button type="button"
                                            @click="closeBlockSettings()"
                                            class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200">
                                        İptal
                                    </button>
                                    <button type="button"
                                            @click="saveBlockSettings()"
                                            class="rounded-lg bg-amber-600 px-4 py-2 text-sm font-medium text-white hover:bg-amber-700">
                                        Kaydet
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <div x-show="rowSettingsModalOpen" x-cloak
                     class="fixed inset-0 z-[70] flex items-center justify-center bg-black/50 p-4"
                     @click.self="closeRowSettings()">
                    <div class="w-full max-w-2xl rounded-2xl bg-white shadow-2xl border border-gray-200 p-5 max-h-[85vh] overflow-y-auto">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <h4 class="text-base font-semibold text-gray-900">Satır Ayarları</h4>
                                <p class="mt-1 text-xs text-gray-500">Container, wrapper ve stil ayarları.</p>
                            </div>
                            <button type="button"
                                    @click="closeRowSettings()"
                                    class="rounded-lg bg-gray-100 px-3 py-2 text-xs text-gray-600 hover:bg-gray-200">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <template x-if="settingsRow">
                            <div class="mt-5 space-y-4">
                                <div class="flex flex-wrap gap-2 border-b border-gray-200 pb-3">
                                    <button type="button" @click="rowSettingsTab = 'layout'" class="rounded-lg px-3 py-1.5 text-xs font-medium"
                                            :class="rowSettingsTab === 'layout' ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'">
                                        Yerleşim
                                    </button>
                                    <button type="button" @click="rowSettingsTab = 'style'" class="rounded-lg px-3 py-1.5 text-xs font-medium"
                                            :class="rowSettingsTab === 'style' ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'">
                                        Stil
                                    </button>
                                    <button type="button" @click="rowSettingsTab = 'code'" class="rounded-lg px-3 py-1.5 text-xs font-medium"
                                            :class="rowSettingsTab === 'code' ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'">
                                        Kod
                                    </button>
                                </div>

                                <div x-show="rowSettingsTab === 'layout'" class="grid gap-4 sm:grid-cols-2">
                                    <div>
                                        <label class="mb-1 block text-xs font-medium text-gray-600">Container</label>
                                        <select x-model="settingsRow.container"
                                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                                            <option value="container">container</option>
                                            <option value="container-fluid">container-fluid</option>
                                            <option value="">Yok</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-xs font-medium text-gray-600">Wrapper Tag</label>
                                        <input type="text" x-model="settingsRow.wrapper_tag"
                                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                                    </div>
                                    <label class="flex items-center gap-2 rounded-lg bg-gray-50 px-3 py-2 text-sm text-gray-700 sm:col-span-2">
                                        <input type="checkbox" x-model="settingsRow.is_active" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        Satır aktif
                                    </label>
                                </div>

                                <div x-show="rowSettingsTab === 'style'" class="grid gap-4 sm:grid-cols-2">
                                    <div>
                                        <label class="mb-1 block text-xs font-medium text-gray-600">CSS Sınıf</label>
                                        <input type="text" x-model="settingsRow.css_class"
                                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-xs font-medium text-gray-600">Element ID</label>
                                        <input type="text" x-model="settingsRow.element_id"
                                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="mb-1 block text-xs font-medium text-gray-600">Inline Style</label>
                                        <input type="text" x-model="settingsRow.inline_style"
                                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="mb-1 block text-xs font-medium text-gray-600">Diğer Attributes</label>
                                        <input type="text" x-model="settingsRow.custom_attributes"
                                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                                    </div>
                                </div>

                                <div x-show="rowSettingsTab === 'code'" class="space-y-4">
                                    <div>
                                        <label class="mb-1 block text-xs font-medium text-gray-600">Wrapper HTML</label>
                                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-3 text-xs text-gray-700 font-mono overflow-x-auto"
                                             x-text="rowPreview(settingsRow)"></div>
                                    </div>
                                </div>

                                <div class="flex items-center justify-end gap-3 border-t border-gray-200 pt-4">
                                    <button type="button"
                                            @click="closeRowSettings()"
                                            class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200">
                                        İptal
                                    </button>
                                    <button type="button"
                                            @click="saveRowSettings()"
                                            class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                                        Kaydet
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <div x-show="columnSettingsModalOpen" x-cloak
                     class="fixed inset-0 z-[70] flex items-center justify-center bg-black/50 p-4"
                     @click.self="closeColumnSettings()">
                    <div class="w-full max-w-3xl rounded-2xl bg-white shadow-2xl border border-gray-200 p-5 max-h-[85vh] overflow-y-auto">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <h4 class="text-base font-semibold text-gray-900">Kolon Ayarları</h4>
                                <p class="mt-1 text-xs text-gray-500">Responsive genişlik, CSS ve wrapper ayarları.</p>
                            </div>
                            <button type="button"
                                    @click="closeColumnSettings()"
                                    class="rounded-lg bg-gray-100 px-3 py-2 text-xs text-gray-600 hover:bg-gray-200">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <template x-if="settingsColumn">
                            <div class="mt-5 space-y-4">
                                <div class="flex flex-wrap gap-2 border-b border-gray-200 pb-3">
                                    <button type="button" @click="columnSettingsTab = 'layout'" class="rounded-lg px-3 py-1.5 text-xs font-medium"
                                            :class="columnSettingsTab === 'layout' ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'">
                                        Yerleşim
                                    </button>
                                    <button type="button" @click="columnSettingsTab = 'style'" class="rounded-lg px-3 py-1.5 text-xs font-medium"
                                            :class="columnSettingsTab === 'style' ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'">
                                        Stil
                                    </button>
                                    <button type="button" @click="columnSettingsTab = 'code'" class="rounded-lg px-3 py-1.5 text-xs font-medium"
                                            :class="columnSettingsTab === 'code' ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'">
                                        Kod
                                    </button>
                                </div>

                                <div class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-xs text-gray-600">
                                    Yerleşim etiketi: <span class="font-semibold text-gray-800" x-text="columnLayoutSummary(settingsColumn)"></span>
                                </div>

                                <div x-show="columnSettingsTab === 'layout'" class="grid gap-4 sm:grid-cols-2">
                                    <div>
                                        <label class="mb-1 block text-xs font-medium text-gray-600">XS (col-)</label>
                                        <select x-model.number="settingsColumn.responsive.xs"
                                                @change="normalizeResponsive(settingsColumn)"
                                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                                            <template x-for="width in [1,2,3,4,5,6,7,8,9,10,11,12]" :key="'xs-' + width">
                                                <option :value="width" x-text="'col-' + width"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <template x-for="breakpoint in ['sm', 'md', 'lg', 'xl']" :key="breakpoint">
                                        <div>
                                            <label class="mb-1 block text-xs font-medium text-gray-600" x-text="breakpoint.toUpperCase() + ' (col-' + breakpoint + '-)'"></label>
                                            <select x-model="settingsColumn.responsive[breakpoint]"
                                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                                                <option value="">Yok</option>
                                                <template x-for="width in [1,2,3,4,5,6,7,8,9,10,11,12]" :key="breakpoint + '-' + width">
                                                    <option :value="width" x-text="'col-' + breakpoint + '-' + width"></option>
                                                </template>
                                            </select>
                                        </div>
                                    </template>
                                    <label class="flex items-center gap-2 rounded-lg bg-gray-50 px-3 py-2 text-sm text-gray-700 sm:col-span-2">
                                        <input type="checkbox" x-model="settingsColumn.is_active" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        Kolon aktif
                                    </label>
                                </div>

                                <div x-show="columnSettingsTab === 'style'" class="grid gap-4 sm:grid-cols-2">
                                    <div>
                                        <label class="mb-1 block text-xs font-medium text-gray-600">CSS Sınıf</label>
                                        <input type="text" x-model="settingsColumn.css_class"
                                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-xs font-medium text-gray-600">Element ID</label>
                                        <input type="text" x-model="settingsColumn.element_id"
                                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="mb-1 block text-xs font-medium text-gray-600">Inline Style</label>
                                        <input type="text" x-model="settingsColumn.inline_style"
                                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="mb-1 block text-xs font-medium text-gray-600">Diğer Attributes</label>
                                        <input type="text" x-model="settingsColumn.custom_attributes"
                                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                                    </div>
                                </div>

                                <div x-show="columnSettingsTab === 'code'" class="space-y-4">
                                    <div>
                                        <label class="mb-1 block text-xs font-medium text-gray-600">Wrapper HTML</label>
                                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-3 text-xs text-gray-700 font-mono overflow-x-auto"
                                             x-text="columnPreview(settingsColumn)"></div>
                                    </div>
                                </div>

                                <div class="flex items-center justify-end gap-3 border-t border-gray-200 pt-4">
                                    <button type="button"
                                            @click="closeColumnSettings()"
                                            class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200">
                                        İptal
                                    </button>
                                    <button type="button"
                                            @click="saveColumnSettings()"
                                            class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                                        Kaydet
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
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

@push('scripts')
<script>
function frontendSectionEditor({ initialRegions = null, availableTemplates = [] }) {
    return {
        regions: { header: [], body: [], footer: [] },
        regionNames: ['header', 'body', 'footer'],
        availableTemplates,
        pickerModalOpen: false,
        pickerSearch: '',
        pickerTarget: null,
        openBlockMenuFor: null,
        settingsModalOpen: false,
        settingsTarget: null,
        settingsTab: 'content',
        settingsDraft: null,
        columnSettingsModalOpen: false,
        columnSettingsTarget: null,
        columnSettingsTab: 'layout',
        columnSettingsDraft: null,
        rowSettingsModalOpen: false,
        rowSettingsTarget: null,
        rowSettingsTab: 'layout',
        rowSettingsDraft: null,

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

        getTemplateByType(type) {
            return this.availableTemplates.find((template) => template.type === type);
        },

        regionLabel(region) {
            return {
                header: 'Header',
                body: 'Body',
                footer: 'Footer',
            }[region] || region;
        },

        regionShellClass(region) {
            return {
                header: 'border-blue-200 bg-blue-50/40',
                body: 'border-green-200 bg-green-50/40',
                footer: 'border-purple-200 bg-purple-50/40',
            }[region] || 'border-gray-200 bg-gray-50/40';
        },

        regionBadgeClass(region) {
            return {
                header: 'bg-blue-200 text-blue-800',
                body: 'bg-green-200 text-green-800',
                footer: 'bg-purple-200 text-purple-800',
            }[region] || 'bg-gray-200 text-gray-800';
        },

        regionButtonClass(region) {
            return {
                header: 'bg-blue-50 text-blue-700 hover:bg-blue-100',
                body: 'bg-green-50 text-green-700 hover:bg-green-100',
                footer: 'bg-purple-50 text-purple-700 hover:bg-purple-100',
            }[region] || 'bg-gray-50 text-gray-700 hover:bg-gray-100';
        },

        rowShellClass(region) {
            return {
                header: 'border-blue-200',
                body: 'border-green-200',
                footer: 'border-purple-200',
            }[region] || 'border-gray-200';
        },

        columnClassLabel(column) {
            const width = column?.responsive?.xs || column?.width || 12;
            return `col-${Math.min(12, Math.max(1, Number(width)))}`;
        },

        editorColumnCanvasStyle(column) {
            const width = Number(column?.responsive?.xs || column?.width || 12);
            const bounded = Math.min(12, Math.max(1, width));

            return {
                gridColumn: `span ${bounded} / span ${bounded}`,
            };
        },

        columnLayoutSummary(column) {
            if (!column) return 'col-12';

            const parts = [];
            const xs = Number(column?.responsive?.xs || column?.width || 12);
            parts.push(`col-${Math.min(12, Math.max(1, xs))}`);

            ['sm', 'md', 'lg', 'xl'].forEach((breakpoint) => {
                const value = column?.responsive?.[breakpoint];
                if (value !== '' && value !== null && value !== undefined) {
                    parts.push(`col-${breakpoint}-${value}`);
                }
            });

            return parts.join(' ');
        },

        canMoveColumn(row, columnIndex, direction) {
            const columns = row?.columns || [];
            const targetIndex = columnIndex + direction;
            return targetIndex >= 0 && targetIndex < columns.length;
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
                    _expanded: row._expanded !== false,
                    container: row.container || 'container',
                    wrapper_tag: row.wrapper_tag || 'section',
                    css_class: row.css_class || '',
                    element_id: row.element_id || '',
                    inline_style: row.inline_style || '',
                    custom_attributes: row.custom_attributes || '',
                    columns: (row.columns || []).map((column, columnIndex) => ({
                        _uid: column._uid || this.generateUid('column'),
                        id: column.id || `col_${region}_${rowIndex + 1}_${columnIndex + 1}`,
                        width: Number(column.width || 12),
                        is_active: column.is_active !== false,
                        responsive: {
                            xs: Number(column?.responsive?.xs || column.width || 12),
                            sm: column?.responsive?.sm ?? '',
                            md: column?.responsive?.md ?? '',
                            lg: column?.responsive?.lg ?? '',
                            xl: column?.responsive?.xl ?? '',
                        },
                        css_class: column.css_class || '',
                        element_id: column.element_id || '',
                        inline_style: column.inline_style || '',
                        custom_attributes: column.custom_attributes || '',
                        blocks: (column.blocks || []).map((block, blockIndex) => this.hydrateBlock(block, region, rowIndex, columnIndex, blockIndex)),
                    })),
                }));
            });

            return output;
        },

        hydrateBlock(block, region, rowIndex, columnIndex, blockIndex) {
            const template = this.getTemplateById(block.section_template_id);
            const hasSchema = block.schema && typeof block.schema === 'object' && Object.keys(block.schema).length > 0;

            return {
                _uid: block._uid || this.generateUid('block'),
                id: block.id || `block_${block.type || 'item'}_${rowIndex + 1}_${columnIndex + 1}_${blockIndex + 1}`,
                type: block.type || template?.type || '',
                variation: block.variation || template?.variation || 'default',
                render_mode: block.render_mode || template?.render_mode || 'html',
                section_template_id: block.section_template_id || template?.id || null,
                template_name: block.template_name || template?.name || '',
                component_key: block.component_key || template?.component_key || null,
                schema: hasSchema ? block.schema : (template?.schema || {}),
                content: JSON.parse(JSON.stringify(block.content || template?.default_content || {})),
                is_active: block.is_active !== false,
                sort_order: block.sort_order || (blockIndex + 1),
                wrapper_tag: block.wrapper_tag || 'section',
                css_class: block.css_class || '',
                element_id: block.element_id || '',
                inline_style: block.inline_style || '',
                custom_attributes: block.custom_attributes || '',
                html_template: block.html_template || template?.html_template || null,
                html_override: block.html_override || '',
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
                _expanded: true,
                container: 'container',
                wrapper_tag: 'section',
                css_class: '',
                element_id: '',
                inline_style: '',
                custom_attributes: '',
                columns: [this.createColumn(region, rowIndex, 0)],
            };
        },

        toggleRowExpand(region, rowIndex) {
            const row = this.regions[region][rowIndex];
            row._expanded = row._expanded === false ? true : false;
        },

        blockSummary(block) {
            const content = block.content || {};
            const summaryKey = ['title', 'subtitle', 'description', 'eyebrow', 'button_text']
                .find((key) => typeof content[key] === 'string' && String(content[key]).trim() !== '');

            if (!summaryKey) {
                return 'Hazır alanlar bu kartın içinde düzenlenir.';
            }

            return String(content[summaryKey]);
        },

        createColumn(region, rowIndex, columnIndex) {
            return {
                _uid: this.generateUid('column'),
                id: `col_${region}_${rowIndex + 1}_${columnIndex + 1}`,
                width: 12,
                is_active: true,
                responsive: { xs: 12, sm: '', md: '', lg: '', xl: '' },
                css_class: '',
                element_id: '',
                inline_style: '',
                custom_attributes: '',
                blocks: [],
            };
        },

        normalizeResponsive(column) {
            column.responsive = column.responsive || { xs: 12, sm: '', md: '', lg: '', xl: '' };
            column.responsive.xs = Math.min(12, Math.max(1, Number(column.responsive.xs || column.width || 12)));
            ['sm', 'md', 'lg', 'xl'].forEach((key) => {
                if (column.responsive[key] === null || column.responsive[key] === undefined) {
                    column.responsive[key] = '';
                }
            });
            column.width = column.responsive.xs;
        },

        getFilteredTemplates(search) {
            const query = String(search || '').trim().toLowerCase();

            if (!query) {
                return this.availableTemplates;
            }

            return this.availableTemplates.filter((template) =>
                [template.name, template.type, template.variation]
                    .filter(Boolean)
                    .some((value) => String(value).toLowerCase().includes(query))
            );
        },

        getRegionPresets(region) {
            const presets = [];

            if (region === 'body') {
                if (this.getTemplateByType('hero')) presets.push({ key: 'hero', label: 'Hero' });
                if (this.getTemplateByType('rich-text')) {
                    presets.push({ key: 'rich-text', label: 'Tanıtım Metni' });
                    presets.push({ key: 'two-column-content', label: '2 Kolon İçerik' });
                }
                if (this.getTemplateByType('features')) presets.push({ key: 'features', label: 'Özellik Alanı' });
                if (this.getTemplateByType('article-list')) presets.push({ key: 'article-list', label: 'Yazı Liste' });
            }

            if (region === 'header' && this.getTemplateByType('rich-text')) {
                presets.push({ key: 'header-basic', label: 'Basit Header' });
            }

            if (region === 'footer' && this.getTemplateByType('rich-text')) {
                presets.push({ key: 'footer-basic', label: 'Basit Footer' });
            }

            return presets;
        },

        addRow(region) {
            this.regions[region].push(this.createRow(region, this.regions[region].length));
            this.normalizeSortOrder();
        },

        createBlockFromTemplate(template, region, rowIndex, columnIndex, blockIndex, overrides = {}) {
            return this.hydrateBlock({
                id: `${template.type}_${blockIndex + 1}`,
                type: template.type,
                variation: template.variation,
                render_mode: template.render_mode,
                section_template_id: template.id,
                template_name: template.name,
                component_key: template.component_key || null,
                schema: template.schema || {},
                content: {
                    ...(template.default_content || {}),
                    ...(overrides.content || {}),
                },
                is_active: true,
                ...overrides,
            }, region, rowIndex, columnIndex, blockIndex);
        },

        applyPreset(region, presetKey) {
            const rows = this.buildPresetRows(region, presetKey, this.regions[region].length);
            if (!rows.length) return;
            this.regions[region].push(...rows);
            this.normalizeSortOrder();
        },

        buildPresetRows(region, presetKey, startIndex) {
            const richText = this.getTemplateByType('rich-text');
            const hero = this.getTemplateByType('hero');
            const features = this.getTemplateByType('features');
            const articleList = this.getTemplateByType('article-list');

            const createRowWithColumns = (widths, blockFactory) => {
                const rowIndex = startIndex;

                return {
                    _uid: this.generateUid('row'),
                    id: `row_${region}_${rowIndex + 1}`,
                    type: 'row',
                    is_active: true,
                    _expanded: true,
                    columns: widths.map((width, columnIndex) => ({
                        ...this.createColumn(region, rowIndex, columnIndex),
                        width,
                        responsive: { xs: width, sm: '', md: '', lg: '', xl: '' },
                        blocks: blockFactory(columnIndex),
                    })),
                };
            };

            switch (presetKey) {
                case 'hero':
                    if (!hero) return [];
                    return [createRowWithColumns([12], () => [
                        this.createBlockFromTemplate(hero, region, startIndex, 0, 0),
                    ])];

                case 'rich-text':
                    if (!richText) return [];
                    return [createRowWithColumns([12], () => [
                        this.createBlockFromTemplate(richText, region, startIndex, 0, 0),
                    ])];

                case 'features':
                    if (!features) return [];
                    return [createRowWithColumns([12], () => [
                        this.createBlockFromTemplate(features, region, startIndex, 0, 0),
                    ])];

                case 'article-list':
                    if (!articleList) return [];
                    return [createRowWithColumns([12], () => [
                        this.createBlockFromTemplate(articleList, region, startIndex, 0, 0),
                    ])];

                case 'two-column-content':
                    if (!richText) return [];
                    return [createRowWithColumns([6, 6], (columnIndex) => [
                        this.createBlockFromTemplate(richText, region, startIndex, columnIndex, 0, {
                            content: {
                                title: columnIndex === 0 ? 'Sol içerik' : 'Sağ içerik',
                            },
                        }),
                    ])];

                case 'header-basic':
                    if (!richText) return [];
                    return [createRowWithColumns([12], () => [
                        this.createBlockFromTemplate(richText, region, startIndex, 0, 0, {
                            content: { title: 'Header alanı' },
                        }),
                    ])];

                case 'footer-basic':
                    if (!richText) return [];
                    return [createRowWithColumns([12], () => [
                        this.createBlockFromTemplate(richText, region, startIndex, 0, 0, {
                            content: { title: 'Footer alanı' },
                        }),
                    ])];

                default:
                    return [];
            }
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

        applyColumnPreset(region, rowIndex, widths) {
            const row = this.regions[region][rowIndex];
            row.columns = widths.map((width, columnIndex) => ({
                ...this.createColumn(region, rowIndex, columnIndex),
                width,
                responsive: { xs: width, sm: '', md: '', lg: '', xl: '' },
            }));
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

        openColumnSettings(region, rowIndex, columnIndex) {
            this.columnSettingsTarget = { region, rowIndex, columnIndex };
            this.columnSettingsTab = 'layout';
            this.columnSettingsDraft = JSON.parse(JSON.stringify(this.regions?.[region]?.[rowIndex]?.columns?.[columnIndex] || null));
            if (this.columnSettingsDraft) {
                this.normalizeResponsive(this.columnSettingsDraft);
            }
            this.columnSettingsModalOpen = true;
        },

        closeColumnSettings() {
            this.columnSettingsModalOpen = false;
            this.columnSettingsTarget = null;
            this.columnSettingsDraft = null;
        },

        saveColumnSettings() {
            if (!this.columnSettingsTarget || !this.columnSettingsDraft) return;
            const { region, rowIndex, columnIndex } = this.columnSettingsTarget;
            this.normalizeResponsive(this.columnSettingsDraft);
            this.regions[region][rowIndex].columns[columnIndex] = {
                ...this.regions[region][rowIndex].columns[columnIndex],
                ...JSON.parse(JSON.stringify(this.columnSettingsDraft)),
            };
            this.normalizeSortOrder();
            this.closeColumnSettings();
        },

        get settingsColumn() {
            return this.columnSettingsDraft;
        },

        openRowSettings(region, rowIndex) {
            this.rowSettingsTarget = { region, rowIndex };
            this.rowSettingsTab = 'layout';
            this.rowSettingsDraft = JSON.parse(JSON.stringify(this.regions?.[region]?.[rowIndex] || null));
            this.rowSettingsModalOpen = true;
        },

        closeRowSettings() {
            this.rowSettingsModalOpen = false;
            this.rowSettingsTarget = null;
            this.rowSettingsDraft = null;
        },

        saveRowSettings() {
            if (!this.rowSettingsTarget || !this.rowSettingsDraft) return;
            const { region, rowIndex } = this.rowSettingsTarget;
            this.regions[region][rowIndex] = {
                ...this.regions[region][rowIndex],
                ...JSON.parse(JSON.stringify(this.rowSettingsDraft)),
            };
            this.normalizeSortOrder();
            this.closeRowSettings();
        },

        get settingsRow() {
            return this.rowSettingsDraft;
        },

        addBlockByTemplate(region, rowIndex, columnIndex, templateId) {
            const column = this.regions[region][rowIndex].columns[columnIndex];
            const template = this.getTemplateById(templateId);
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

            this.normalizeSortOrder();
        },

        openBlockPicker(region, rowIndex, columnIndex) {
            this.pickerTarget = { region, rowIndex, columnIndex };
            this.pickerSearch = '';
            this.pickerModalOpen = true;
        },

        closeBlockPicker() {
            this.pickerModalOpen = false;
            this.pickerSearch = '';
            this.pickerTarget = null;
        },

        pickTemplate(templateId) {
            if (!this.pickerTarget) return;
            const { region, rowIndex, columnIndex } = this.pickerTarget;
            this.addBlockByTemplate(region, rowIndex, columnIndex, templateId);
            this.closeBlockPicker();
        },

        openBlockSettings(region, rowIndex, columnIndex, blockIndex) {
            this.settingsTarget = { region, rowIndex, columnIndex, blockIndex };
            this.settingsTab = 'content';
            this.settingsDraft = JSON.parse(JSON.stringify(this.regions?.[region]?.[rowIndex]?.columns?.[columnIndex]?.blocks?.[blockIndex] || null));
            this.settingsModalOpen = true;
        },

        closeBlockSettings() {
            this.settingsModalOpen = false;
            this.settingsTarget = null;
            this.settingsDraft = null;
        },

        saveBlockSettings() {
            if (!this.settingsTarget || !this.settingsDraft) return;
            const { region, rowIndex, columnIndex, blockIndex } = this.settingsTarget;
            this.regions[region][rowIndex].columns[columnIndex].blocks[blockIndex] = {
                ...this.regions[region][rowIndex].columns[columnIndex].blocks[blockIndex],
                ...JSON.parse(JSON.stringify(this.settingsDraft)),
            };
            this.normalizeSortOrder();
            this.closeBlockSettings();
        },

        get settingsBlock() {
            return this.settingsDraft;
        },

        blockRenderedHtml(block) {
            if (!block) return '';
            const template = String(block.html_override || block.html_template || '').trim();
            const rawPattern = new RegExp('\\{\\{\\{\\s*([a-zA-Z0-9_]+)\\s*\\}\\}\\}', 'g');
            const safePattern = new RegExp('\\{\\{\\s*([a-zA-Z0-9_]+)\\s*\\}\\}', 'g');

            if (!template) {
                return '<div class="text-xs text-gray-500">Bu block için HTML template tanımlı değil.</div>';
            }

            return template.replace(rawPattern, (_match, key) => {
                return String(block.content?.[key] ?? '');
            }).replace(safePattern, (_match, key) => {
                return this.escapeHtml(block.content?.[key] ?? '');
            });
        },

        blockCodePreview(block) {
            const wrapperTag = block?.wrapper_tag || 'section';
            const classAttr = block?.css_class ? ` class="${block.css_class}"` : '';
            const idAttr = block?.element_id ? ` id="${block.element_id}"` : '';
            const styleAttr = block?.inline_style ? ` style="${block.inline_style}"` : '';
            const extraAttr = block?.custom_attributes ? ` ${block.custom_attributes}` : '';
            const inner = this.blockRenderedHtml(block);

            return `<${wrapperTag}${idAttr}${classAttr}${styleAttr}${extraAttr}>${inner}</${wrapperTag}>`;
        },

        rowPreview(row) {
            if (!row) return '';
            const wrapperTag = row.wrapper_tag || 'section';
            const container = row.container || 'container';
            const classAttr = row.css_class ? ` ${row.css_class}` : '';
            const idAttr = row.element_id ? ` id="${row.element_id}"` : '';
            const styleAttr = row.inline_style ? ` style="${row.inline_style}"` : '';
            const extraAttr = row.custom_attributes ? ` ${row.custom_attributes}` : '';

            return `<${wrapperTag}${idAttr} class="${container}${classAttr}"${styleAttr}${extraAttr}>...</${wrapperTag}>`;
        },

        columnPreview(column) {
            if (!column) return '';
            const classes = [this.columnClassLabel(column)];
            ['sm', 'md', 'lg', 'xl'].forEach((bp) => {
                if (column?.responsive?.[bp]) {
                    classes.push(`col-${bp}-${column.responsive[bp]}`);
                }
            });
            if (column?.css_class) classes.push(column.css_class);
            const idAttr = column?.element_id ? ` id="${column.element_id}"` : '';
            const styleAttr = column?.inline_style ? ` style="${column.inline_style}"` : '';
            const extraAttr = column?.custom_attributes ? ` ${column.custom_attributes}` : '';

            return `<div${idAttr} class="${classes.join(' ')}"${styleAttr}${extraAttr}>...</div>`;
        },

        escapeHtml(value) {
            return String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        },

        removeBlock(region, rowIndex, columnIndex, blockIndex) {
            this.regions[region][rowIndex].columns[columnIndex].blocks.splice(blockIndex, 1);
            this.normalizeSortOrder();
        },

        duplicateBlock(region, rowIndex, columnIndex, blockIndex) {
            const blocks = this.regions[region][rowIndex].columns[columnIndex].blocks;
            const source = blocks[blockIndex];
            if (!source) return;

            const clone = JSON.parse(JSON.stringify(source));
            clone._uid = this.generateUid('block');
            clone.id = `${source.type || 'block'}_${blocks.length + 1}`;
            blocks.splice(blockIndex + 1, 0, clone);
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
                    container: row.container || 'container',
                    wrapper_tag: row.wrapper_tag || 'section',
                    css_class: row.css_class || null,
                    element_id: row.element_id || null,
                    inline_style: row.inline_style || null,
                    custom_attributes: row.custom_attributes || null,
                    columns: (row.columns || []).map((column, columnIndex) => ({
                        id: column.id || `col_${region}_${rowIndex + 1}_${columnIndex + 1}`,
                        width: Number(column.width || 12),
                        is_active: column.is_active !== false,
                        responsive: {
                            xs: Number(column?.responsive?.xs || column.width || 12),
                            sm: column?.responsive?.sm || null,
                            md: column?.responsive?.md || null,
                            lg: column?.responsive?.lg || null,
                            xl: column?.responsive?.xl || null,
                        },
                        css_class: column.css_class || null,
                        element_id: column.element_id || null,
                        inline_style: column.inline_style || null,
                        custom_attributes: column.custom_attributes || null,
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
                            wrapper_tag: block.wrapper_tag || 'section',
                            css_class: block.css_class || null,
                            element_id: block.element_id || null,
                            inline_style: block.inline_style || null,
                            custom_attributes: block.custom_attributes || null,
                            html_override: block.html_override || null,
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
                    row.container = row.container || 'container';
                    row.wrapper_tag = row.wrapper_tag || 'section';
                    row.css_class = row.css_class || '';
                    row.element_id = row.element_id || '';
                    row.inline_style = row.inline_style || '';
                    row.custom_attributes = row.custom_attributes || '';

                    (row.columns || []).forEach((column, columnIndex) => {
                        column.id = column.id || `col_${region}_${rowIndex + 1}_${columnIndex + 1}`;
                        this.normalizeResponsive(column);
                        column.css_class = column.css_class || '';
                        column.element_id = column.element_id || '';
                        column.inline_style = column.inline_style || '';
                        column.custom_attributes = column.custom_attributes || '';

                        (column.blocks || []).forEach((block, blockIndex) => {
                            block.sort_order = blockIndex + 1;
                            block.wrapper_tag = block.wrapper_tag || 'section';
                            block.css_class = block.css_class || '';
                            block.element_id = block.element_id || '';
                            block.inline_style = block.inline_style || '';
                            block.custom_attributes = block.custom_attributes || '';
                            block.html_override = block.html_override || '';
                        });
                    });
                });
            });
        },
    };
}
</script>
@endpush
