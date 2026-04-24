@if($editorData->shouldRenderFrontendEditor())
<div x-show="builderMode === 'frontend'" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6"
     x-data="frontendSectionEditor({{ \Illuminate\Support\Js::from($editorData->frontendSectionEditorPayload()) }})"
     x-on:frontend-block-focus.window="focusBlock($event.detail.blockId)">
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
            <div class="mt-3 rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs text-emerald-800">
                <div class="font-semibold">Önerilen akış</div>
                <p class="mt-1 leading-5">
                    Yeni bir tasarım section’ı doğrudan sayfa içinde üretme. Önce
                    <a href="{{ route('admin.section-templates.create') }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="font-medium underline decoration-emerald-400 underline-offset-2 hover:text-emerald-900">
                        Block Şablonları
                    </a>
                    ekranında şablon oluştur, sonra burada <strong>Block Ekle</strong> ile kullan. Böylece aynı block başka sayfa ve firmalarda tekrar kullanılabilir.
                </p>
            </div>
            <div class="mt-3 rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-xs text-blue-800">
                Geçiş katmanı aktif: mevcut block listesi arka planda <code>Header / Body / Footer</code> yapısına normalize ediliyor.
                Sonraki adımda editör doğrudan bu region yapısını gösterecek.
            </div>
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
                                                <div class="rounded-xl border border-indigo-200 bg-white p-4"
                                                     :id="'builder-block-' + block.id">
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

    @include('admin.pages._form.editor-modals')

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
