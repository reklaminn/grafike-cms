{{-- Visual Layout Builder - Alpine.js + SortableJS --}}
@php
    $layoutBuilderModules = collect(config('modules'))
        ->map(function ($module, $id) {
            return [
                'id' => $id,
                'name' => $module['name'],
                'configSchema' => $module['configSchema'] ?? [],
            ];
        })
        ->values()
        ->all();

    $layoutBuilderArticles = isset($page)
        ? $page->articles()
            ->latest()
            ->get(['id', 'title', 'status'])
            ->map(fn ($article) => [
                'id' => $article->id,
                'title' => $article->title,
                'status' => $article->status,
                'editUrl' => route('admin.articles.edit', $article),
            ])
            ->values()
            ->all()
        : [];
@endphp

<div x-data="layoutBuilder()" x-init="init()" class="space-y-4">

    {{-- Toolbar --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
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
        <div class="flex items-center gap-2">
            <button type="button" @click="toggleJsonView()"
                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-gray-100 text-gray-600 text-xs font-medium rounded-lg hover:bg-gray-200 transition-colors">
                <i class="fas fa-code"></i> <span x-text="showJson ? 'Builder' : 'JSON'"></span>
            </button>
            <button type="button" @click="collapseAll()"
                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-gray-100 text-gray-600 text-xs font-medium rounded-lg hover:bg-gray-200 transition-colors">
                <i class="fas fa-compress-alt"></i> Daralt
            </button>
        </div>
    </div>

    {{-- JSON Raw Editor (toggle) --}}
    <div x-show="showJson" x-cloak class="relative">
        <textarea x-model="rawJson" @input.debounce.500ms="parseRawJson()"
                  rows="12"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm font-mono focus:ring-2 focus:ring-indigo-500 focus:border-transparent"></textarea>
        <template x-if="jsonError">
            <p class="mt-1 text-xs text-red-500" x-text="jsonError"></p>
        </template>
    </div>

    {{-- Visual Builder --}}
    <div x-show="!showJson" class="space-y-3" id="layout-rows-container">

        <template x-if="rows.length === 0">
            <div class="text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                <i class="fas fa-layer-group text-4xl text-gray-300 mb-3"></i>
                <p class="text-sm text-gray-500">Henüz satır eklenmemiş</p>
                <p class="text-xs text-gray-400 mt-1">Yukarıdaki butonlarla satır ekleyin</p>
            </div>
        </template>

        {{-- Rows --}}
        <template x-for="(row, rowIndex) in rows" :key="row._id">
            <div class="bg-white border-2 rounded-xl overflow-hidden transition-all"
                 :class="{
                     'border-blue-300': row.type === 'header',
                     'border-green-300': row.type === 'body',
                     'border-purple-300': row.type === 'footer'
                 }">

                {{-- Row Header --}}
                <div class="flex items-center justify-between px-4 py-2 cursor-move"
                     :class="{
                         'bg-blue-50': row.type === 'header',
                         'bg-green-50': row.type === 'body',
                         'bg-purple-50': row.type === 'footer'
                     }"
                     :data-row-handle="rowIndex">

                    <div class="flex items-center gap-2">
                        <i class="fas fa-grip-vertical text-gray-400 row-drag-handle cursor-grab"></i>
                        <span class="text-xs font-bold uppercase px-2 py-0.5 rounded"
                              :class="{
                                  'bg-blue-200 text-blue-800': row.type === 'header',
                                  'bg-green-200 text-green-800': row.type === 'body',
                                  'bg-purple-200 text-purple-800': row.type === 'footer'
                              }"
                              x-text="row.type"></span>
                        <span class="text-xs text-gray-500" x-text="row.cont || 'container'"></span>
                        <span x-show="row.elcss" class="text-xs text-gray-400" x-text="'.' + row.elcss"></span>
                        <span x-show="row.active === false" class="text-[10px] font-semibold uppercase tracking-wider text-red-500">Pasif</span>
                    </div>

                    <div class="flex items-center gap-1">
                        <button type="button" @click="toggleRowActive(rowIndex)" class="p-1 text-gray-400 hover:text-amber-600" title="Aktif/Pasif">
                            <i class="fas" :class="row.active === false ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                        <button type="button" @click="toggleRowExpand(rowIndex)" class="p-1 text-gray-400 hover:text-gray-600" title="Genişlet/Daralt">
                            <i class="fas" :class="row._expanded ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                        </button>
                        <button type="button" @click="editRowSettings(rowIndex)" class="p-1 text-gray-400 hover:text-indigo-600" title="Satır Ayarları">
                            <i class="fas fa-cog"></i>
                        </button>
                        <button type="button" @click="duplicateRow(rowIndex)" class="p-1 text-gray-400 hover:text-green-600" title="Kopyala">
                            <i class="fas fa-copy"></i>
                        </button>
                        <button type="button" @click="removeRow(rowIndex)" class="p-1 text-gray-400 hover:text-red-600" title="Sil">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>

                {{-- Row Content (Columns) --}}
                <div x-show="row._expanded" class="p-4">

                    <div class="flex items-center gap-2 mb-3">
                        <button type="button" @click="addColumn(rowIndex)"
                                class="inline-flex items-center gap-1 px-2 py-1 bg-indigo-50 text-indigo-600 text-xs font-medium rounded hover:bg-indigo-100 transition-colors">
                            <i class="fas fa-columns"></i> Kolon Ekle
                        </button>
                        <span class="text-xs text-gray-400">
                            Toplam: <span x-text="getColumnTotal(rowIndex)"></span>/12
                        </span>
                    </div>

                    {{-- Columns Grid Preview --}}
                    <div class="flex gap-2 flex-wrap" :id="'columns-' + row._id">
                        <template x-for="(col, colIndex) in getColumns(rowIndex)" :key="col._id">
                            <div class="border border-gray-200 rounded-lg bg-gray-50 flex-shrink-0 min-w-0 transition-all"
                                 :class="{ 'opacity-50 ring-1 ring-red-200': col.active === false }"
                                 :style="'flex: 0 0 calc(' + (getColWidth(col) / 12 * 100) + '% - 0.5rem); max-width: calc(' + (getColWidth(col) / 12 * 100) + '% - 0.5rem);'">

                                {{-- Column Header --}}
                                <div class="flex items-center justify-between px-3 py-1.5 bg-gray-100 rounded-t-lg col-drag-handle cursor-grab">
                                    <div class="flex items-center gap-1">
                                        <i class="fas fa-grip-vertical text-gray-400 text-xs"></i>
                                        <span class="text-xs font-medium text-gray-600" x-text="col.coltype || 'col-12'"></span>
                                        <span x-show="col.active === false" class="text-[10px] font-semibold uppercase tracking-wider text-red-500">Pasif</span>
                                    </div>
                                    <div class="flex items-center gap-0.5">
                                        <button type="button" @click="toggleColumnActive(rowIndex, colIndex)" class="p-0.5 text-gray-400 hover:text-amber-600" title="Aktif/Pasif">
                                            <i class="fas text-xs" :class="col.active === false ? 'fa-eye-slash' : 'fa-eye'"></i>
                                        </button>
                                        <button type="button" @click="editColumnSettings(rowIndex, colIndex)" class="p-0.5 text-gray-400 hover:text-indigo-600" title="Kolon Ayarları">
                                            <i class="fas fa-cog text-xs"></i>
                                        </button>
                                        <button type="button" @click="duplicateColumn(rowIndex, colIndex)" class="p-0.5 text-gray-400 hover:text-green-600" title="Kopyala">
                                            <i class="fas fa-copy text-xs"></i>
                                        </button>
                                        <button type="button" @click="removeColumn(rowIndex, colIndex)" class="p-0.5 text-gray-400 hover:text-red-600" title="Sil">
                                            <i class="fas fa-times text-xs"></i>
                                        </button>
                                    </div>
                                </div>

                                {{-- Modules in Column --}}
                                <div class="p-2 min-h-[60px] space-y-1.5" :id="'modules-' + col._id">
                                    <template x-for="(mod, modIndex) in getModules(rowIndex, colIndex)" :key="mod._id">
                                        <div class="space-y-1.5 module-sortable-item">
                                            <div class="flex items-center justify-between px-2 py-1.5 bg-white border border-indigo-200 rounded text-xs module-drag-handle cursor-grab group"
                                                 :class="{ 'opacity-50 ring-1 ring-red-200': mod.active === false }">
                                                <div class="flex items-center gap-1.5 min-w-0">
                                                    <i class="fas fa-grip-vertical text-gray-300"></i>
                                                    <i class="fas fa-puzzle-piece text-indigo-400"></i>
                                                    <span class="text-gray-700 truncate" x-text="getModuleName(mod.modulid)"></span>
                                                    <span x-show="mod.active === false" class="text-[10px] font-semibold uppercase tracking-wider text-red-500">Pasif</span>
                                                </div>
                                                <div class="flex items-center gap-0.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                                    <button type="button" @click="toggleModuleActive(rowIndex, colIndex, modIndex)"
                                                            class="p-0.5 text-gray-400 hover:text-amber-600" title="Aktif/Pasif">
                                                        <i class="fas text-xs" :class="mod.active === false ? 'fa-eye-slash' : 'fa-eye'"></i>
                                                    </button>
                                                    <button type="button" @click="editModuleConfig(rowIndex, colIndex, modIndex)"
                                                            class="p-0.5 text-gray-400 hover:text-indigo-600" title="Modül Ayarları">
                                                        <i class="fas fa-cog text-xs"></i>
                                                    </button>
                                                    <button type="button" @click="removeModule(rowIndex, colIndex, modIndex)"
                                                            class="p-0.5 text-gray-400 hover:text-red-600" title="Sil">
                                                        <i class="fas fa-times text-xs"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div x-show="mod.modulid == 90" class="rounded border border-dashed border-gray-200 bg-white p-2 text-[11px] text-gray-600">
                                                <div class="mb-1 font-semibold text-gray-500">Bu sayfadaki yazılar</div>
                                                <template x-if="pageArticles.length === 0">
                                                    <div class="text-gray-400">Henüz yazı yok.</div>
                                                </template>
                                                <div class="space-y-1">
                                                    <template x-for="article in pageArticles" :key="article.id">
                                                        <a :href="article.editUrl" class="flex items-center justify-between gap-2 rounded px-2 py-1 hover:bg-gray-50">
                                                            <span class="truncate" x-text="article.title"></span>
                                                            <span class="shrink-0 text-[10px]" :class="article.status === 'published' ? 'text-green-600' : 'text-amber-600'" x-text="article.status"></span>
                                                        </a>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                    </template>

                                    {{-- Add Module Button --}}
                                    <button type="button" @click="showModulePicker(rowIndex, colIndex)"
                                            class="w-full py-2 border-2 border-dashed border-gray-300 rounded text-gray-400 text-xs hover:border-indigo-400 hover:text-indigo-500 transition-colors">
                                        <i class="fas fa-plus mr-1"></i> Modül Ekle
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </template>
    </div>

    {{-- Hidden input for form submission --}}
    <input type="hidden" name="layout_json" :value="getJsonOutput()">

    {{-- Row Settings Modal --}}
    <div x-show="showRowModal" x-cloak
         class="fixed inset-0 z-[60] flex items-center justify-center bg-black bg-opacity-50"
         @click.self="showRowModal = false">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 max-h-[80vh] overflow-y-auto">
            <div class="flex items-center justify-between px-6 py-4 border-b">
                <h3 class="text-base font-semibold text-gray-800">Satır Ayarları</h3>
                <button type="button" @click="showRowModal = false" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Section Tipi</label>
                    <select x-model="editingRow.type" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        <option value="header">Header</option>
                        <option value="body">Body</option>
                        <option value="footer">Footer</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Container Tipi</label>
                    <select x-model="editingRow.cont" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        <option value="container">container</option>
                        <option value="container-fluid">container-fluid</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">CSS Sınıf</label>
                    <input type="text" x-model="editingRow.elcss" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="my-section bg-dark">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Element ID</label>
                    <input type="text" x-model="editingRow.elid" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="section-hero">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Inline Style</label>
                    <input type="text" x-model="editingRow.elstyle" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="background: #f5f5f5;">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Diğer Özellikler</label>
                    <input type="text" x-model="editingRow.elother" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder='data-aos="fade-up"'>
                </div>
                <label class="flex items-center gap-2">
                    <input type="checkbox" x-model="editingRow.active" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="text-sm text-gray-700">Satır aktif</span>
                </label>
            </div>
            <div class="px-6 py-4 border-t flex justify-end gap-2">
                <button type="button" @click="showRowModal = false" class="px-4 py-2 text-sm text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">İptal</button>
                <button type="button" @click="saveRowSettings()" class="px-4 py-2 text-sm text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">Kaydet</button>
            </div>
        </div>
    </div>

    {{-- Column Settings Modal --}}
    <div x-show="showColModal" x-cloak
         class="fixed inset-0 z-[60] flex items-center justify-center bg-black bg-opacity-50"
         @click.self="showColModal = false">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 max-h-[80vh] overflow-y-auto">
            <div class="flex items-center justify-between px-6 py-4 border-b">
                <h3 class="text-base font-semibold text-gray-800">Kolon Ayarları</h3>
                <button type="button" @click="showColModal = false" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">XS (col-)</label>
                        <select x-model="editingCol.coltype" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            <option value="">Yok</option>
                            <template x-for="i in 12"><option :value="'col-' + i" x-text="'col-' + i"></option></template>
                            <option value="col">col (auto)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">SM (col-sm-)</label>
                        <select x-model="editingCol.colsmtype" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            <option value="">Yok</option>
                            <template x-for="i in 12"><option :value="'col-sm-' + i" x-text="'col-sm-' + i"></option></template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">MD (col-md-)</label>
                        <select x-model="editingCol.colmdtype" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            <option value="">Yok</option>
                            <template x-for="i in 12"><option :value="'col-md-' + i" x-text="'col-md-' + i"></option></template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">LG (col-lg-)</label>
                        <select x-model="editingCol.collgtype" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            <option value="">Yok</option>
                            <template x-for="i in 12"><option :value="'col-lg-' + i" x-text="'col-lg-' + i"></option></template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">XL (col-xl-)</label>
                        <select x-model="editingCol.colxltype" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            <option value="">Yok</option>
                            <template x-for="i in 12"><option :value="'col-xl-' + i" x-text="'col-xl-' + i"></option></template>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">CSS Sınıf</label>
                    <input type="text" x-model="editingCol.celcss" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="p-3 bg-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Element ID</label>
                    <input type="text" x-model="editingCol.celid" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Inline Style</label>
                    <input type="text" x-model="editingCol.celstyle" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Diğer Özellikler</label>
                    <input type="text" x-model="editingCol.celother" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                </div>
                <label class="flex items-center gap-2">
                    <input type="checkbox" x-model="editingCol.active" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="text-sm text-gray-700">Kolon aktif</span>
                </label>
            </div>
            <div class="px-6 py-4 border-t flex justify-end gap-2">
                <button type="button" @click="showColModal = false" class="px-4 py-2 text-sm text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">İptal</button>
                <button type="button" @click="saveColSettings()" class="px-4 py-2 text-sm text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">Kaydet</button>
            </div>
        </div>
    </div>

    {{-- Module Picker Modal --}}
    <div x-show="showModuleModal" x-cloak
         class="fixed inset-0 z-[60] flex items-center justify-center bg-black bg-opacity-50"
         @click.self="showModuleModal = false">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 max-h-[80vh] overflow-y-auto">
            <div class="flex items-center justify-between px-6 py-4 border-b">
                <h3 class="text-base font-semibold text-gray-800">Modül Seçin</h3>
                <button type="button" @click="showModuleModal = false" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-4">
                <input type="text" x-model="moduleSearch" placeholder="Modül ara..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm mb-3 focus:ring-2 focus:ring-indigo-500">
                <div class="space-y-1 max-h-64 overflow-y-auto">
                    <template x-for="mod in filteredModules()" :key="mod.id">
                        <button type="button" @click="selectModule(mod.id)"
                                class="w-full text-left px-3 py-2.5 rounded-lg text-sm hover:bg-indigo-50 hover:text-indigo-700 transition-colors flex items-center gap-2">
                            <i class="fas fa-puzzle-piece text-indigo-400"></i>
                            <span x-text="mod.name"></span>
                            <span class="text-xs text-gray-400 ml-auto" x-text="'#' + mod.id"></span>
                        </button>
                    </template>
                </div>
            </div>
        </div>
    </div>

    {{-- Module Config Modal --}}
    <div x-show="showModuleConfigModal" x-cloak
         class="fixed inset-0 z-[60] flex items-center justify-center bg-black bg-opacity-50"
         @click.self="showModuleConfigModal = false">
        <div class="bg-white rounded-xl shadow-2xl w-full mx-4 overflow-y-auto"
             :class="isCustomHtmlModule() ? 'max-w-[92vw] max-h-[90vh]' : 'max-w-lg max-h-[80vh]'">
            <div class="flex items-center justify-between px-6 py-4 border-b">
                <h3 class="text-base font-semibold text-gray-800">
                    Modül Yapılandırma: <span class="text-indigo-600" x-text="getModuleName(editingModule.modulid)"></span>
                </h3>
                <button type="button" @click="showModuleConfigModal = false" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div x-show="isCustomHtmlModule()" class="space-y-3">
                    <div class="flex flex-wrap gap-2 border-b border-gray-200 pb-3">
                        <button type="button"
                                @click="moduleConfigTab = 'content'"
                                class="rounded-lg px-3 py-1.5 text-xs font-medium"
                                :class="moduleConfigTab === 'content' ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'">
                            Kod
                        </button>
                        <button type="button"
                                @click="moduleConfigTab = 'preview'"
                                class="rounded-lg px-3 py-1.5 text-xs font-medium"
                                :class="moduleConfigTab === 'preview' ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'">
                            HTML Önizleme
                        </button>
                    </div>
                    <div class="rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-xs text-blue-800">
                        Bu modül legacy ajans akışı için hızlı özel HTML ekleme alanıdır. Kod sekmesinde HTML yapıştır, Önizleme sekmesinde sonucu kontrol et.
                    </div>
                </div>

                <div x-show="getEditingModuleSchema().length > 0" class="space-y-4">
                    <template x-for="field in getEditingModuleSchema()" :key="field.name">
                        <div class="gap-2"
                             :class="isCustomHtmlField(field) ? 'flex flex-col' : 'flex items-center'"
                             x-show="!isCustomHtmlModule() || moduleConfigTab === 'content'">
                            <label class="text-sm font-medium text-gray-700"
                                   :class="isCustomHtmlField(field) ? '' : 'w-44 shrink-0'"
                                   x-text="field.label || field.name"></label>

                            <template x-if="field.type === 'select'">
                                <select
                                    :value="getSchemaFieldValue(field)"
                                    @change="setSchemaFieldValue(field.name, $event.target.value)"
                                    class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm"
                                >
                                    <option value="">Seçin</option>
                                    <template x-for="option in getSchemaOptions(field)" :key="option">
                                        <option :value="option" x-text="option"></option>
                                    </template>
                                </select>
                            </template>

                            <template x-if="field.type === 'boolean'">
                                <select
                                    :value="getSchemaFieldValue(field)"
                                    @change="setSchemaFieldValue(field.name, $event.target.value)"
                                    class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm"
                                >
                                    <option value="1">Evet</option>
                                    <option value="0">Hayır</option>
                                </select>
                            </template>

                            <template x-if="field.type === 'textarea'">
                                <div class="flex-1">
                                    <template x-if="isCustomHtmlField(field)">
                                        <div class="space-y-3">
                                            <div class="flex flex-wrap items-center justify-between gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2">
                                                <div class="text-xs text-slate-600">
                                                    <span class="font-medium text-slate-700">Satır:</span>
                                                    <span x-text="getCustomHtmlLineCount()"></span>
                                                </div>
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <button type="button"
                                                            @click="undoCustomHtml()"
                                                            class="rounded-lg bg-gray-100 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-200">
                                                        <i class="fas fa-rotate-left mr-1"></i> Undo
                                                    </button>
                                                    <button type="button"
                                                            @click="redoCustomHtml()"
                                                            class="rounded-lg bg-gray-100 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-200">
                                                        <i class="fas fa-rotate-right mr-1"></i> Redo
                                                    </button>
                                                    <button type="button"
                                                            @click="findInCustomHtml()"
                                                            class="rounded-lg bg-gray-100 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-200">
                                                        <i class="fas fa-magnifying-glass mr-1"></i> Find
                                                    </button>
                                                    <button type="button"
                                                            @click="replaceInCustomHtml()"
                                                            class="rounded-lg bg-gray-100 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-200">
                                                        <i class="fas fa-arrows-rotate mr-1"></i> Replace
                                                    </button>
                                                    <button type="button"
                                                            @click="goToCustomHtmlLine()"
                                                            class="rounded-lg bg-gray-100 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-200">
                                                        <i class="fas fa-arrow-turn-down mr-1"></i> Go to
                                                    </button>
                                                    <button type="button"
                                                            @click="customHtmlDoubleHeight = !customHtmlDoubleHeight; refreshCustomHtmlCodeMirror()"
                                                            class="rounded-lg bg-gray-100 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-200">
                                                        <i class="fas fa-up-right-and-down-left-from-center mr-1"></i> 2x
                                                    </button>
                                                    <button type="button"
                                                            @click="insertImageIntoCustomHtml()"
                                                            class="rounded-lg bg-gray-100 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-200">
                                                        <i class="fas fa-image mr-1"></i> Resim Ekle
                                                    </button>
                                                    <button type="button"
                                                            @click="autocompleteCustomHtml()"
                                                            class="rounded-lg bg-gray-100 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-200">
                                                        <i class="fas fa-bolt mr-1"></i> Autocomplete
                                                    </button>
                                                    <button type="button"
                                                            @click="beautifyCustomHtml()"
                                                            class="rounded-lg bg-indigo-100 px-3 py-1.5 text-xs font-medium text-indigo-700 hover:bg-indigo-200">
                                                        <i class="fas fa-wand-magic-sparkles mr-1"></i> Beautify
                                                    </button>
                                                    <button type="button"
                                                            disabled
                                                            class="rounded-lg bg-violet-100 px-3 py-1.5 text-xs font-medium text-violet-700 opacity-60 cursor-not-allowed">
                                                        <i class="fas fa-robot mr-1"></i> AI Block Oluştur
                                                    </button>
                                                    <button type="button"
                                                            @click="collapseAllCustomHtmlFolds()"
                                                            class="rounded-lg bg-gray-100 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-200">
                                                        Ağacı Kapat
                                                    </button>
                                                    <button type="button"
                                                            @click="expandAllCustomHtmlFolds()"
                                                            class="rounded-lg bg-gray-100 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-200">
                                                        Ağacı Aç
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="overflow-hidden rounded-xl border border-slate-700 bg-slate-950 shadow-inner">
                                                <textarea
                                                    x-ref="customHtmlTextarea"
                                                    :placeholder="field.name"
                                                    class="hidden"
                                                    spellcheck="false"
                                                    x-text="getCustomHtmlBody()"
                                                ></textarea>
                                                <div class="custom-html-codemirror-shell"
                                                     :class="customHtmlDoubleHeight ? 'is-double' : ''">
                                                    <div x-ref="customHtmlCodeMirrorMount"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>

                                    <template x-if="!isCustomHtmlField(field)">
                                        <textarea
                                            @input="setSchemaFieldValue(field.name, $event.target.value)"
                                            :placeholder="field.name"
                                            class="w-full min-h-[120px] px-3 py-2 border border-gray-300 rounded-lg text-sm"
                                            x-text="getSchemaFieldValue(field)"
                                        ></textarea>
                                    </template>
                                </div>
                            </template>

                            <template x-if="field.type !== 'select' && field.type !== 'boolean' && field.type !== 'textarea'">
                                <input
                                    :type="field.type === 'number' ? 'number' : 'text'"
                                    :value="getSchemaFieldValue(field)"
                                    @input="setSchemaFieldValue(field.name, $event.target.value)"
                                    :placeholder="field.name"
                                    class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm"
                                >
                            </template>
                        </div>
                    </template>
                </div>

                <div x-show="isCustomHtmlModule() && moduleConfigTab === 'preview'" class="space-y-3">
                    <div class="rounded-lg border border-gray-200 bg-white p-4">
                        <div class="mb-2 text-xs font-medium text-gray-500">Render Önizleme</div>
                        <div class="max-h-[420px] overflow-auto rounded-lg border border-dashed border-gray-200 p-4"
                             x-html="getCustomHtmlBody()"></div>
                    </div>
                </div>

                <div x-show="getCustomModuleParams().length > 0 || getEditingModuleSchema().length === 0" class="space-y-3">
                    <div x-show="getEditingModuleSchema().length > 0" class="text-xs font-semibold uppercase tracking-wider text-gray-400">
                        Diğer Parametreler
                    </div>
                    <template x-for="(param, paramIndex) in getCustomModuleParams()" :key="param._key || paramIndex">
                        <div class="flex items-center gap-2">
                            <input type="text" x-model="param.name" placeholder="Parametre adı"
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            <input type="text" x-model="param.value" placeholder="Değer"
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            <button type="button" @click="removeCustomModuleParam(paramIndex)"
                                    class="p-2 text-red-400 hover:text-red-600">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </template>
                </div>

                <button type="button" @click="addModuleParam()"
                        class="inline-flex items-center gap-1 px-3 py-1.5 bg-gray-100 text-gray-600 text-xs font-medium rounded-lg hover:bg-gray-200">
                    <i class="fas fa-plus"></i> Parametre Ekle
                </button>
            </div>
            <div class="px-6 py-4 border-t flex justify-end gap-2">
                <button type="button" @click="showModuleConfigModal = false" class="px-4 py-2 text-sm text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">İptal</button>
                <button type="button" @click="saveModuleConfig()" class="px-4 py-2 text-sm text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">Kaydet</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/codemirror.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/theme/material-darker.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/fold/foldgutter.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/dialog/dialog.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/hint/show-hint.min.css">
<style>
    .custom-html-codemirror-shell .CodeMirror {
        height: 62vh;
        background: #020617;
        color: #99f6e4;
        font-size: 0.95rem;
        line-height: 1.5rem;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
    }

    .custom-html-codemirror-shell.is-double .CodeMirror {
        height: 78vh;
    }

    .custom-html-codemirror-shell .CodeMirror-gutters {
        background: #0f172a;
        border-right: 1px solid #1e293b;
    }

    .custom-html-codemirror-shell .CodeMirror-linenumber {
        color: #94a3b8;
    }

    .custom-html-codemirror-shell .CodeMirror-foldmarker {
        color: #67e8f9;
        text-shadow: none;
        font-weight: 700;
        font-family: inherit;
    }

    .custom-html-codemirror-shell .CodeMirror-cursor {
        border-left: 1px solid #6ee7b7;
    }

    .custom-html-codemirror-shell .CodeMirror-selected {
        background: rgba(59, 130, 246, 0.18);
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/codemirror.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/xml/xml.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/javascript/javascript.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/css/css.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/htmlmixed/htmlmixed.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/fold/foldcode.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/fold/foldgutter.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/fold/xml-fold.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/search/searchcursor.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/search/search.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/dialog/dialog.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/edit/closetag.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/edit/matchtags.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/hint/show-hint.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/hint/xml-hint.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/hint/html-hint.min.js"></script>
<script>
function layoutBuilder() {
    return {
        rows: [],
        showJson: false,
        rawJson: '',
        jsonError: '',
        showRowModal: false,
        showColModal: false,
        showModuleModal: false,
        showModuleConfigModal: false,
        editingRow: {},
        editingRowIndex: null,
        editingCol: {},
        editingColIndex: null,
        editingColRowIndex: null,
        editingModule: {},
        editingModuleIndices: {},
        moduleConfigTab: 'content',
        customHtmlTreeExpanded: true,
        customHtmlDoubleHeight: false,
        customHtmlCodeMirror: null,
        customHtmlCollapsedStarts: [],
        customHtmlHistory: [],
        customHtmlFuture: [],
        moduleSearch: '',
        pickerTarget: {},
        _idCounter: 0,
        pageArticles: @json($layoutBuilderArticles),
        availableModules: @json($layoutBuilderModules),

        generateId() {
            return '_lb_' + (++this._idCounter) + '_' + Math.random().toString(36).substr(2, 5);
        },

        init() {
            // Parse existing layout_json from page
            const existing = @json(isset($page) && $page->layout_json ? $page->layout_json : []);
            if (existing && Array.isArray(existing) && existing.length > 0) {
                this.rows = existing.map(row => this.normalizeRow(row));
            }
            this.rawJson = JSON.stringify(this.getCleanRows(), null, 2);

            this.$nextTick(() => this.initSortable());
        },

        normalizeRow(row) {
            row._id = this.generateId();
            row._expanded = true;
            row.type = row.type || 'body';
            row.cont = row.cont || 'container';
            row.elcss = row.elcss || '';
            row.elid = row.elid || '';
            row.elstyle = row.elstyle || '';
            row.elother = row.elother || '';
            row.active = row.active !== false;

            if (!row.children) row.children = [];
            // Normalize children: each child should be an array of columns
            row.children = row.children.map(colGroup => {
                if (!Array.isArray(colGroup)) colGroup = [colGroup];
                return colGroup.map(col => this.normalizeColumn(col));
            });

            return row;
        },

        normalizeColumn(col) {
            col._id = this.generateId();
            col.coltype = col.coltype || 'col-12';
            col.colsmtype = col.colsmtype || '';
            col.colmdtype = col.colmdtype || '';
            col.collgtype = col.collgtype || '';
            col.colxltype = col.colxltype || '';
            col.celcss = col.celcss || '';
            col.celid = col.celid || '';
            col.celstyle = col.celstyle || '';
            col.celother = col.celother || '';
            col.active = col.active !== false;

            if (!col.children) col.children = [];
            col.children = col.children.map(modGroup => {
                if (!Array.isArray(modGroup)) modGroup = [modGroup];
                return modGroup.map(mod => this.normalizeModule(mod));
            });

            return col;
        },

        normalizeModule(mod) {
            mod._id = this.generateId();
            mod.modulid = mod.modulid || 0;
            if (!mod.json) mod.json = [];
            mod.active = mod.active !== false;
            return mod;
        },

        // Row operations
        addRow(type) {
            const row = this.normalizeRow({
                type: type,
                cont: 'container',
                children: [[{
                    coltype: 'col-12',
                    children: []
                }]]
            });
            this.rows.push(row);
            this.updateJson();
            this.$nextTick(() => this.initSortable());
        },

        removeRow(index) {
            if (confirm('Bu satırı silmek istediğinize emin misiniz?')) {
                this.rows.splice(index, 1);
                this.updateJson();
            }
        },

        duplicateRow(index) {
            const copy = JSON.parse(JSON.stringify(this.rows[index]));
            const normalized = this.normalizeRow(copy);
            this.rows.splice(index + 1, 0, normalized);
            this.updateJson();
            this.$nextTick(() => this.initSortable());
        },

        toggleRowExpand(index) {
            this.rows[index]._expanded = !this.rows[index]._expanded;
        },

        toggleRowActive(index) {
            this.rows[index].active = this.rows[index].active === false;
            this.updateJson();
        },

        collapseAll() {
            this.rows.forEach(row => row._expanded = false);
        },

        editRowSettings(index) {
            this.editingRowIndex = index;
            this.editingRow = { ...this.rows[index] };
            this.showRowModal = true;
        },

        saveRowSettings() {
            const row = this.rows[this.editingRowIndex];
            row.type = this.editingRow.type;
            row.cont = this.editingRow.cont;
            row.elcss = this.editingRow.elcss;
            row.elid = this.editingRow.elid;
            row.elstyle = this.editingRow.elstyle;
            row.elother = this.editingRow.elother;
            row.active = this.editingRow.active !== false;
            this.showRowModal = false;
            this.updateJson();
        },

        // Column operations
        getColumns(rowIndex) {
            const row = this.rows[rowIndex];
            if (!row || !row.children) return [];
            // Flatten: children is array of column groups
            let cols = [];
            row.children.forEach(group => {
                if (Array.isArray(group)) {
                    group.forEach(col => cols.push(col));
                }
            });
            return cols;
        },

        getColWidth(col) {
            const type = col.coltype || 'col-12';
            const match = type.match(/col-(\d+)/);
            return match ? parseInt(match[1]) : 12;
        },

        getColumnTotal(rowIndex) {
            return this.getColumns(rowIndex).reduce((sum, col) => sum + this.getColWidth(col), 0);
        },

        addColumn(rowIndex) {
            const col = this.normalizeColumn({ coltype: 'col-6', children: [] });
            if (!this.rows[rowIndex].children.length) {
                this.rows[rowIndex].children.push([col]);
            } else {
                this.rows[rowIndex].children[0].push(col);
            }
            this.updateJson();
            this.$nextTick(() => this.initSortable());
        },

        removeColumn(rowIndex, colIndex) {
            const cols = this.getColumns(rowIndex);
            if (cols.length <= 1) {
                alert('En az bir kolon olmalı. Satırı silmek için satır silme butonunu kullanın.');
                return;
            }
            // Remove from children structure
            let idx = 0;
            for (let g = 0; g < this.rows[rowIndex].children.length; g++) {
                const group = this.rows[rowIndex].children[g];
                for (let c = 0; c < group.length; c++) {
                    if (idx === colIndex) {
                        group.splice(c, 1);
                        if (group.length === 0) this.rows[rowIndex].children.splice(g, 1);
                        this.updateJson();
                        return;
                    }
                    idx++;
                }
            }
        },

        duplicateColumn(rowIndex, colIndex) {
            const cols = this.getColumns(rowIndex);
            const copy = JSON.parse(JSON.stringify(cols[colIndex]));
            const normalized = this.normalizeColumn(copy);
            // Add after current column
            if (this.rows[rowIndex].children[0]) {
                this.rows[rowIndex].children[0].splice(colIndex + 1, 0, normalized);
            }
            this.updateJson();
            this.$nextTick(() => this.initSortable());
        },

        editColumnSettings(rowIndex, colIndex) {
            const col = this.getColumns(rowIndex)[colIndex];
            this.editingColRowIndex = rowIndex;
            this.editingColIndex = colIndex;
            this.editingCol = { ...col };
            this.showColModal = true;
        },

        toggleColumnActive(rowIndex, colIndex) {
            const col = this.getColumns(rowIndex)[colIndex];
            col.active = col.active === false;
            this.updateJson();
        },

        saveColSettings() {
            const col = this.getColumns(this.editingColRowIndex)[this.editingColIndex];
            col.coltype = this.editingCol.coltype;
            col.colsmtype = this.editingCol.colsmtype;
            col.colmdtype = this.editingCol.colmdtype;
            col.collgtype = this.editingCol.collgtype;
            col.colxltype = this.editingCol.colxltype;
            col.celcss = this.editingCol.celcss;
            col.celid = this.editingCol.celid;
            col.celstyle = this.editingCol.celstyle;
            col.celother = this.editingCol.celother;
            col.active = this.editingCol.active !== false;
            this.showColModal = false;
            this.updateJson();
        },

        // Module operations
        getModules(rowIndex, colIndex) {
            const col = this.getColumns(rowIndex)[colIndex];
            if (!col || !col.children) return [];
            let mods = [];
            col.children.forEach(group => {
                if (Array.isArray(group)) {
                    group.forEach(mod => mods.push(mod));
                }
            });
            return mods;
        },

        getModuleName(moduleId) {
            const mod = this.availableModules.find(m => m.id == moduleId);
            return mod ? mod.name : 'Bilinmeyen Modül #' + moduleId;
        },

        getModuleDefinition(moduleId) {
            return this.availableModules.find(m => m.id == moduleId) || null;
        },

        getModuleSchema(moduleId) {
            return this.getModuleDefinition(moduleId)?.configSchema || [];
        },

        getEditingModuleSchema() {
            return this.getModuleSchema(this.editingModule.modulid);
        },

        isCustomHtmlModule(moduleId = null) {
            const currentModuleId = moduleId ?? this.editingModule.modulid;
            return String(currentModuleId) === '1506';
        },

        isCustomHtmlField(field) {
            return this.isCustomHtmlModule() && field?.name === 'body';
        },

        getCustomHtmlBody() {
            const entry = this.getSchemaFieldEntry('body');
            return String(entry?.value ?? '');
        },

        setCustomHtmlBody(value, { pushHistory = false } = {}) {
            const normalized = String(value ?? '');

            if (pushHistory) {
                const last = this.customHtmlHistory[this.customHtmlHistory.length - 1];
                if (last !== normalized) {
                    this.customHtmlHistory.push(normalized);
                    if (this.customHtmlHistory.length > 150) {
                        this.customHtmlHistory.shift();
                    }
                }
                this.customHtmlFuture = [];
            }

            this.setSchemaFieldValue('body', normalized);

            this.$nextTick(() => {
                if (this.customHtmlCodeMirror && this.customHtmlCodeMirror.getValue() !== normalized) {
                    const cursor = this.customHtmlCodeMirror.getCursor();
                    this.customHtmlCodeMirror.setValue(normalized);
                    this.customHtmlCodeMirror.setCursor(cursor);
                    this.customHtmlCodeMirror.refresh();
                } else if (this.$refs.customHtmlTextarea && this.$refs.customHtmlTextarea.value !== normalized) {
                    this.$refs.customHtmlTextarea.value = normalized;
                }
            });
        },

        handleCustomHtmlInput(value) {
            this.setCustomHtmlBody(value, { pushHistory: true });
        },

        ensureCustomHtmlCodeMirror() {
            if (!this.isCustomHtmlModule() || typeof window.CodeMirror === 'undefined' || !this.$refs.customHtmlTextarea) {
                return;
            }

            if (this.customHtmlCodeMirror) {
                this.refreshCustomHtmlCodeMirror();
                if (this.customHtmlCodeMirror.getValue() !== this.getCustomHtmlBody()) {
                    this.customHtmlCodeMirror.setValue(this.getCustomHtmlBody());
                }
                return;
            }

            this.customHtmlCodeMirror = window.CodeMirror.fromTextArea(this.$refs.customHtmlTextarea, {
                mode: 'htmlmixed',
                theme: 'material-darker',
                lineNumbers: true,
                lineWrapping: true,
                viewportMargin: Infinity,
                autoCloseTags: true,
                matchTags: { bothTags: true },
                gutters: ['CodeMirror-linenumbers', 'CodeMirror-foldgutter'],
                foldGutter: true,
                extraKeys: {
                    'Ctrl-Space': 'autocomplete',
                    'Cmd-Space': 'autocomplete',
                    'Ctrl-F': 'findPersistent',
                    'Cmd-F': 'findPersistent',
                    'Ctrl-H': 'replace',
                    'Cmd-Alt-F': 'replace',
                    'Ctrl-Q': (cm) => cm.foldCode(cm.getCursor()),
                    'Tab': (cm) => cm.execCommand('insertSoftTab'),
                },
            });

            this.customHtmlCodeMirror.setValue(this.getCustomHtmlBody());
            this.customHtmlCodeMirror.scrollTo(null, 0);
            this.customHtmlCodeMirror.setCursor({ line: 0, ch: 0 });
            this.customHtmlCodeMirror.on('change', (cm) => {
                const value = cm.getValue();
                const last = this.customHtmlHistory[this.customHtmlHistory.length - 1];
                if (last !== value) {
                    this.customHtmlHistory.push(value);
                    if (this.customHtmlHistory.length > 150) {
                        this.customHtmlHistory.shift();
                    }
                    this.customHtmlFuture = [];
                }
                this.setSchemaFieldValue('body', value);
            });

            this.refreshCustomHtmlCodeMirror();
        },

        refreshCustomHtmlCodeMirror() {
            if (!this.customHtmlCodeMirror) return;
            this.customHtmlCodeMirror.setSize('100%', this.customHtmlDoubleHeight ? '78vh' : '62vh');
            this.$nextTick(() => {
                this.customHtmlCodeMirror.refresh();
            });
        },

        destroyCustomHtmlCodeMirror() {
            if (!this.customHtmlCodeMirror) return;

            const value = this.customHtmlCodeMirror.getValue();
            this.customHtmlCodeMirror.toTextArea();
            this.customHtmlCodeMirror = null;

            if (this.$refs.customHtmlTextarea) {
                this.$refs.customHtmlTextarea.value = value;
            }
        },

        prepareCustomHtmlEdit(event) {
            if (this.customHtmlCodeMirror) return;
            if (!this.customHtmlCollapsedStarts.length) return;

            const navigationKeys = ['ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight', 'PageUp', 'PageDown', 'Home', 'End', 'Escape', 'Tab'];
            if (navigationKeys.includes(event.key) || event.metaKey || event.ctrlKey || event.altKey) return;

            const editor = event.target;
            const currentDisplayLine = editor.value.slice(0, editor.selectionStart).split('\n').length;
            this.customHtmlCollapsedStarts = [];

            this.$nextTick(() => {
                const raw = this.getCustomHtmlBody();
                editor.value = raw;
                const position = this.findPositionForCustomHtmlLine(currentDisplayLine);
                editor.setSelectionRange(position, position);
                this.syncCustomHtmlGutter({ target: editor });
            });
        },

        getCustomHtmlLineNumbers() {
            return Array.from({ length: this.getCustomHtmlLineCount() }, (_value, index) => index + 1);
        },

        getCustomHtmlLineCount() {
            return Math.max(1, this.getCustomHtmlBody().split('\n').length);
        },

        getCustomHtmlGutterEntries() {
            const lines = this.getCustomHtmlBody().split('\n');
            const entries = [];
            let depth = 0;
            const foldPairs = this.getCustomHtmlFoldPairs();

            lines.forEach((line, index) => {
                const marker = this.getCustomHtmlTreeMarker(line);
                const trimmed = String(line || '').trim();

                let entryDepth = depth;

                if (marker === '-') {
                    entryDepth = Math.max(depth - 1, 0);
                    depth = entryDepth;
                }

                entries.push({
                    line: index + 1,
                    marker,
                    depth: entryDepth,
                    toggleable: Boolean(foldPairs[index + 1]),
                });

                if (marker === '+') {
                    depth += 1;
                }

                if (this.isInlineWrappedHtml(trimmed)) {
                    depth = Math.max(depth, entryDepth);
                }
            });

            return entries;
        },

        getCustomHtmlDisplayPayload() {
            const rawLines = this.getCustomHtmlBody().split('\n');
            const foldPairs = this.getCustomHtmlFoldPairs();
            const gutterEntries = this.getCustomHtmlGutterEntries();
            const collapsed = new Set(this.customHtmlCollapsedStarts);
            const displayLines = [];
            const logicalEntries = [];

            for (let lineNumber = 1; lineNumber <= rawLines.length; lineNumber += 1) {
                const gutterEntry = gutterEntries.find((entry) => entry.line === lineNumber) || { line: lineNumber, marker: '·', depth: 0, toggleable: false };

                if (collapsed.has(lineNumber) && foldPairs[lineNumber]) {
                    const closingLine = foldPairs[lineNumber];
                    displayLines.push(rawLines[lineNumber - 1]);
                    logicalEntries.push({
                        ...gutterEntry,
                        marker: '+',
                        sourceLine: lineNumber,
                    });

                    if (closingLine - lineNumber > 1) {
                        displayLines.push(`${'  '.repeat(gutterEntry.depth + 1)}<!-- ${closingLine - lineNumber - 1} satır gizli -->`);
                        logicalEntries.push({
                            line: logicalEntries.length + 1,
                            marker: '·',
                            depth: gutterEntry.depth + 1,
                            toggleable: false,
                            sourceLine: null,
                        });
                    }

                    displayLines.push(rawLines[closingLine - 1]);
                    logicalEntries.push({
                        line: logicalEntries.length + 1,
                        marker: '·',
                        depth: gutterEntry.depth,
                        toggleable: false,
                        sourceLine: closingLine,
                    });

                    lineNumber = closingLine;
                    continue;
                }

                displayLines.push(rawLines[lineNumber - 1]);
                logicalEntries.push({
                    ...gutterEntry,
                    marker: gutterEntry.toggleable ? '-' : gutterEntry.marker,
                    sourceLine: lineNumber,
                });
            }

            return {
                text: displayLines.join('\n'),
                lines: displayLines,
                logicalEntries: logicalEntries.map((entry, index) => ({
                    ...entry,
                    line: index + 1,
                })),
            };
        },

        getCustomHtmlDisplayBody() {
            return this.getCustomHtmlDisplayPayload().text;
        },

        getCustomHtmlDisplayGutterEntries() {
            const payload = this.getCustomHtmlDisplayPayload();
            const visualEntries = [];

            payload.lines.forEach((line, index) => {
                const entry = payload.logicalEntries[index];
                const wrapCount = this.measureCustomHtmlWrapCount(line);

                visualEntries.push({
                    ...entry,
                    key: `line-${entry.line}-0`,
                    label: entry.line,
                });

                for (let continuation = 1; continuation < wrapCount; continuation += 1) {
                    visualEntries.push({
                        ...entry,
                        key: `line-${entry.line}-${continuation}`,
                        label: '',
                        marker: '·',
                        toggleable: false,
                    });
                }
            });

            return visualEntries;
        },

        measureCustomHtmlWrapCount(line) {
            const editor = this.$refs.customHtmlEditor;
            if (!editor) return 1;

            const styles = window.getComputedStyle(editor);
            const font = `${styles.fontStyle} ${styles.fontWeight} ${styles.fontSize} / ${styles.lineHeight} ${styles.fontFamily}`;
            const canvas = this._customHtmlMeasureCanvas || (this._customHtmlMeasureCanvas = document.createElement('canvas'));
            const context = canvas.getContext('2d');
            context.font = font;

            const paddingLeft = parseFloat(styles.paddingLeft || '0');
            const paddingRight = parseFloat(styles.paddingRight || '0');
            const availableWidth = Math.max(editor.clientWidth - paddingLeft - paddingRight - 4, 40);
            const measured = context.measureText(String(line || '')).width;

            return Math.max(1, Math.ceil(measured / availableWidth));
        },

        getCustomHtmlTreeMarker(line) {
            const value = String(line || '').trim();
            if (value === '') return '·';
            if (/^<\//.test(value)) return '-';
            if (this.isInlineWrappedHtml(value)) return '·';
            if (this.isOpeningHtmlTag(value) && !this.isSelfClosingHtml(value)) return '+';
            return '·';
        },

        isInlineWrappedHtml(value) {
            return /^<([a-zA-Z0-9:-]+)([^>]*)>.*<\/\1>$/.test(value);
        },

        isOpeningHtmlTag(value) {
            return /^<([a-zA-Z0-9:-]+)(\s[^>]*)?>$/.test(value);
        },

        isSelfClosingHtml(value) {
            return /\/>$/.test(value) || /^<(img|input|br|hr|meta|link|source|area|base|col|embed|param|track|wbr)\b/i.test(value);
        },

        getCustomHtmlFoldPairs() {
            const lines = this.getCustomHtmlBody().split('\n');
            const stack = [];
            const pairs = {};

            lines.forEach((line, index) => {
                const value = String(line || '').trim();
                const lineNumber = index + 1;

                if (!value || this.isInlineWrappedHtml(value) || this.isSelfClosingHtml(value)) {
                    return;
                }

                const closingMatch = value.match(/^<\/([a-zA-Z0-9:-]+)/);
                if (closingMatch) {
                    const tag = closingMatch[1].toLowerCase();
                    for (let pointer = stack.length - 1; pointer >= 0; pointer -= 1) {
                        if (stack[pointer].tag === tag) {
                            const open = stack.splice(pointer, 1)[0];
                            pairs[open.line] = lineNumber;
                            break;
                        }
                    }
                    return;
                }

                const openingMatch = value.match(/^<([a-zA-Z0-9:-]+)/);
                if (openingMatch) {
                    stack.push({ tag: openingMatch[1].toLowerCase(), line: lineNumber });
                }
            });

            return pairs;
        },

        toggleCustomHtmlFold(sourceLine) {
            const foldPairs = this.getCustomHtmlFoldPairs();
            if (!foldPairs[sourceLine]) return;

            const index = this.customHtmlCollapsedStarts.indexOf(sourceLine);
            if (index === -1) {
                this.customHtmlCollapsedStarts.push(sourceLine);
            } else {
                this.customHtmlCollapsedStarts.splice(index, 1);
            }

            this.customHtmlCollapsedStarts.sort((left, right) => left - right);
        },

        findPositionForCustomHtmlLine(lineNumber) {
            const lines = this.getCustomHtmlBody().split('\n');
            let position = 0;

            for (let index = 0; index < Math.min(Math.max(lineNumber - 1, 0), lines.length); index += 1) {
                position += lines[index].length + 1;
            }

            return position;
        },

        syncCustomHtmlGutter(event) {
            if (this.$refs.customHtmlGutter) {
                this.$refs.customHtmlGutter.scrollTop = event.target.scrollTop;
            }
            if (this.$refs.customHtmlOverlay) {
                this.$refs.customHtmlOverlay.scrollTop = event.target.scrollTop;
                this.$refs.customHtmlOverlay.scrollLeft = event.target.scrollLeft;
            }
        },

        syncCustomHtmlEditorFromGutter(event) {
            if (this.$refs.customHtmlEditor) {
                this.$refs.customHtmlEditor.scrollTop = event.target.scrollTop;
            }
        },

        undoCustomHtml() {
            if (this.customHtmlCodeMirror) {
                this.customHtmlCodeMirror.undo();
                return;
            }
            if (this.customHtmlHistory.length <= 1) return;

            const current = this.customHtmlHistory.pop();
            this.customHtmlFuture.push(current);
            const previous = this.customHtmlHistory[this.customHtmlHistory.length - 1] ?? '';
            this.setCustomHtmlBody(previous);
        },

        redoCustomHtml() {
            if (this.customHtmlCodeMirror) {
                this.customHtmlCodeMirror.redo();
                return;
            }
            if (this.customHtmlFuture.length === 0) return;

            const next = this.customHtmlFuture.pop();
            this.customHtmlHistory.push(next);
            this.setCustomHtmlBody(next);
        },

        beautifyCustomHtml() {
            const current = this.customHtmlCodeMirror ? this.customHtmlCodeMirror.getValue() : this.getCustomHtmlBody();
            const formatted = this.formatHtml(current);
            this.setCustomHtmlBody(formatted, { pushHistory: true });
            this.refreshCustomHtmlCodeMirror();
        },

        findInCustomHtml() {
            if (this.customHtmlCodeMirror) {
                this.customHtmlCodeMirror.execCommand('findPersistent');
                return;
            }
            const query = window.prompt('Bulunacak metni girin:');
            if (!query || !this.$refs.customHtmlEditor) return;

            const editor = this.$refs.customHtmlEditor;
            const haystack = editor.value;
            const startFrom = editor.selectionEnd || 0;
            let index = haystack.indexOf(query, startFrom);
            if (index === -1) {
                index = haystack.indexOf(query);
            }
            if (index === -1) {
                window.alert('Aranan metin bulunamadı.');
                return;
            }

            editor.focus();
            editor.setSelectionRange(index, index + query.length);
        },

        replaceInCustomHtml() {
            if (this.customHtmlCodeMirror) {
                this.customHtmlCodeMirror.execCommand('replace');
                return;
            }
            if (!this.$refs.customHtmlEditor) return;
            const find = window.prompt('Değiştirilecek metin:');
            if (!find) return;
            const replace = window.prompt('Yeni değer:');
            if (replace === null) return;

            const updated = this.getCustomHtmlBody().split(find).join(replace);
            this.setCustomHtmlBody(updated, { pushHistory: true });
        },

        goToCustomHtmlLine() {
            const line = Number(window.prompt('Gitmek istediğiniz satır numarası:') || 1);
            const safeLine = Math.max(1, Math.floor(line));
            if (this.customHtmlCodeMirror) {
                this.customHtmlCodeMirror.focus();
                this.customHtmlCodeMirror.setCursor({ line: safeLine - 1, ch: 0 });
                return;
            }
            if (!this.$refs.customHtmlEditor) return;
            const lines = this.getCustomHtmlBody().split('\n');
            let position = 0;

            for (let index = 0; index < Math.min(safeLine - 1, lines.length); index += 1) {
                position += lines[index].length + 1;
            }

            this.$refs.customHtmlEditor.focus();
            this.$refs.customHtmlEditor.setSelectionRange(position, position);
            this.syncCustomHtmlGutter({ target: this.$refs.customHtmlEditor });
        },

        insertAtCustomHtmlCursor(snippet) {
            if (this.customHtmlCodeMirror) {
                this.customHtmlCodeMirror.focus();
                this.customHtmlCodeMirror.replaceSelection(snippet);
                return;
            }

            if (!this.$refs.customHtmlEditor) {
                this.setCustomHtmlBody(this.getCustomHtmlBody() + snippet, { pushHistory: true });
                return;
            }

            const editor = this.$refs.customHtmlEditor;
            const start = editor.selectionStart ?? editor.value.length;
            const end = editor.selectionEnd ?? editor.value.length;
            const nextValue = `${editor.value.slice(0, start)}${snippet}${editor.value.slice(end)}`;

            this.setCustomHtmlBody(nextValue, { pushHistory: true });

            this.$nextTick(() => {
                const cursor = start + snippet.length;
                editor.focus();
                editor.setSelectionRange(cursor, cursor);
            });
        },

        insertImageIntoCustomHtml() {
            const src = window.prompt('Resim yolu / URL:');
            if (!src) return;
            const alt = window.prompt('Resim adı / alt metni:', '') ?? '';
            const snippet = `<img src="${src}" alt="${alt}">`;
            this.insertAtCustomHtmlCursor(snippet);
        },

        autocompleteCustomHtml() {
            if (this.customHtmlCodeMirror && typeof this.customHtmlCodeMirror.showHint === 'function') {
                this.customHtmlCodeMirror.focus();
                this.customHtmlCodeMirror.showHint({ completeSingle: false });
                return;
            }
            const suggestions = ['section', 'div', 'header', 'footer', 'main', 'article', 'img', 'a', 'ul', 'li', 'button'];
            const choice = window.prompt(`Etiket veya snippet seçin:\n${suggestions.join(', ')}`, 'div');
            if (!choice) return;

            const tag = choice.trim().toLowerCase();
            const selfClosing = ['img', 'input', 'br', 'hr', 'meta', 'link'].includes(tag);
            const snippet = selfClosing ? `<${tag}>` : `<${tag}></${tag}>`;
            this.insertAtCustomHtmlCursor(snippet);
        },

        collapseAllCustomHtmlFolds() {
            this.customHtmlTreeExpanded = false;
            if (!this.customHtmlCodeMirror) return;

            const lineCount = this.customHtmlCodeMirror.lineCount();
            for (let line = 0; line < lineCount; line += 1) {
                this.customHtmlCodeMirror.foldCode({ line, ch: 0 }, null, 'fold');
            }
        },

        expandAllCustomHtmlFolds() {
            this.customHtmlTreeExpanded = true;
            if (!this.customHtmlCodeMirror) return;

            const lineCount = this.customHtmlCodeMirror.lineCount();
            for (let line = 0; line < lineCount; line += 1) {
                this.customHtmlCodeMirror.foldCode({ line, ch: 0 }, null, 'unfold');
            }
        },

        highlightCustomHtmlOverlay() {
            return this.getCustomHtmlDisplayBody()
                .split('\n')
                .map((line) => this.highlightCustomHtmlLine(line))
                .join('\n');
        },

        highlightCustomHtmlLine(line) {
            const escaped = this.escapeHtml(line);

            return escaped.replace(
                /(&lt;\/?)([a-zA-Z0-9:-]+)(.*?)(\/?&gt;)/g,
                (_match, open, tag, attrs, close) => {
                    return `${open}<span class="text-sky-300">${tag}</span>${this.highlightCustomHtmlAttributes(attrs)}${close}`;
                }
            );
        },

        highlightCustomHtmlAttributes(attrs) {
            return String(attrs || '')
                .replace(
                    /(\s)([a-zA-Z_:][-a-zA-Z0-9_:.]*)(=)(&quot;.*?&quot;|&#039;.*?&#039;)/g,
                    (_match, spacing, name, equals, value) => {
                        return `${spacing}<span class="text-violet-300">${name}</span><span class="text-slate-400">${equals}</span><span class="text-amber-300">${value}</span>`;
                    }
                );
        },

        formatHtml(html) {
            const source = String(html || '').trim();
            if (!source) return '';

            const tokens = source
                .replace(/>\s+</g, '><')
                .replace(/</g, '\n<')
                .replace(/>/g, '>\n')
                .split('\n')
                .map((line) => line.trim())
                .filter(Boolean);

            let depth = 0;

            return tokens.map((token) => {
                if (/^<\//.test(token)) {
                    depth = Math.max(depth - 1, 0);
                }

                const line = `${'  '.repeat(depth)}${token}`;

                if (/^<[^!/][^>]*[^/]?>$/.test(token) && !/^<[^>]+>.*<\/[^>]+>$/.test(token)) {
                    depth += 1;
                }

                return line;
            }).join('\n');
        },

        renderCustomHtmlTree() {
            const html = this.getCustomHtmlBody().trim();
            if (!html) {
                return '<div class="rounded-lg border border-dashed border-gray-200 bg-gray-50 px-3 py-4 text-gray-400">HTML ağacı burada görünecek.</div>';
            }

            try {
                const documentTree = new DOMParser().parseFromString(`<body>${html}</body>`, 'text/html');
                const nodes = Array.from(documentTree.body.childNodes)
                    .filter((node) => node.nodeType === Node.ELEMENT_NODE || (node.nodeType === Node.TEXT_NODE && node.textContent.trim() !== ''));

                return nodes.map((node) => this.renderHtmlTreeNode(node, 0)).join('');
            } catch (_error) {
                return '<div class="rounded-lg border border-red-200 bg-red-50 px-3 py-4 text-red-600">HTML ağacı oluşturulamadı.</div>';
            }
        },

        renderHtmlTreeNode(node, depth = 0) {
            const padding = depth * 14;

            if (node.nodeType === Node.TEXT_NODE) {
                const text = this.escapeHtml(node.textContent.trim());
                if (!text) return '';

                return `<div style="padding-left:${padding}px" class="py-1 text-gray-400">#text ${text.slice(0, 40)}</div>`;
            }

            const tag = node.tagName.toLowerCase();
            const attrs = Array.from(node.attributes || [])
                .filter((attribute) => ['id', 'class'].includes(attribute.name))
                .map((attribute) => `${attribute.name}="${this.escapeHtml(attribute.value)}"`)
                .join(' ');
            const label = `&lt;${tag}${attrs ? ' ' + attrs : ''}&gt;`;
            const children = Array.from(node.childNodes || [])
                .filter((child) => child.nodeType === Node.ELEMENT_NODE || (child.nodeType === Node.TEXT_NODE && child.textContent.trim() !== ''))
                .map((child) => this.renderHtmlTreeNode(child, depth + 1))
                .join('');
            const open = this.customHtmlTreeExpanded ? 'open' : '';

            if (!children) {
                return `<div style="padding-left:${padding}px" class="py-1 text-slate-700">${label}</div>`;
            }

            return `<details ${open} class="py-1"><summary style="padding-left:${padding}px" class="cursor-pointer list-none text-slate-700">${label}</summary><div class="mt-1 border-l border-gray-200">${children}</div></details>`;
        },

        escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        },

        getSchemaOptions(field) {
            if (Array.isArray(field.options)) return field.options;
            if (typeof field.options === 'string') {
                return field.options.split(',').map(option => option.trim()).filter(Boolean);
            }
            return [];
        },

        ensureEditingModuleJson() {
            if (!this.editingModule.json) this.editingModule.json = [];
        },

        getSchemaFieldEntry(name) {
            this.ensureEditingModuleJson();
            return this.editingModule.json.find(param => param.name === name);
        },

        getSchemaFieldValue(field) {
            const entry = this.getSchemaFieldEntry(field.name);
            if (entry) return entry.value;
            return field.default ?? '';
        },

        setSchemaFieldValue(name, value) {
            this.ensureEditingModuleJson();
            const existing = this.editingModule.json.find(param => param.name === name);
            if (existing) {
                existing.value = value;
                return;
            }

            this.editingModule.json.push({ name, value });
        },

        getCustomModuleParams() {
            this.ensureEditingModuleJson();
            const schemaNames = this.getEditingModuleSchema().map(field => field.name);
            return this.editingModule.json.filter(param => !schemaNames.includes(param.name));
        },

        removeCustomModuleParam(paramIndex) {
            const customParams = this.getCustomModuleParams();
            const target = customParams[paramIndex];
            if (!target) return;

            const actualIndex = this.editingModule.json.findIndex(param => param.name === target.name && param.value === target.value);
            if (actualIndex !== -1) {
                this.editingModule.json.splice(actualIndex, 1);
            }
        },

        applyModuleSchemaDefaults() {
            const schema = this.getEditingModuleSchema();
            schema.forEach(field => {
                const existing = this.getSchemaFieldEntry(field.name);
                if (!existing && Object.prototype.hasOwnProperty.call(field, 'default')) {
                    this.editingModule.json.push({ name: field.name, value: field.default });
                }
            });
        },

        showModulePicker(rowIndex, colIndex) {
            this.pickerTarget = { rowIndex, colIndex };
            this.moduleSearch = '';
            this.showModuleModal = true;
        },

        filteredModules() {
            if (!this.moduleSearch) return this.availableModules;
            const search = this.moduleSearch.toLowerCase();
            return this.availableModules.filter(m =>
                m.name.toLowerCase().includes(search) || String(m.id).includes(search)
            );
        },

        selectModule(moduleId) {
            const { rowIndex, colIndex } = this.pickerTarget;
            const col = this.getColumns(rowIndex)[colIndex];
            const mod = this.normalizeModule({ modulid: moduleId, json: [] });

            const schema = this.getModuleSchema(moduleId);
            mod.json = schema
                .filter(field => Object.prototype.hasOwnProperty.call(field, 'default'))
                .map(field => ({ name: field.name, value: field.default }));

            if (!col.children.length) {
                col.children.push([mod]);
            } else {
                col.children[0].push(mod);
            }

            this.showModuleModal = false;
            this.updateJson();
            this.$nextTick(() => this.initSortable());
        },

        removeModule(rowIndex, colIndex, modIndex) {
            const col = this.getColumns(rowIndex)[colIndex];
            let idx = 0;
            for (let g = 0; g < col.children.length; g++) {
                const group = col.children[g];
                for (let m = 0; m < group.length; m++) {
                    if (idx === modIndex) {
                        group.splice(m, 1);
                        if (group.length === 0) col.children.splice(g, 1);
                        this.updateJson();
                        return;
                    }
                    idx++;
                }
            }
        },

        editModuleConfig(rowIndex, colIndex, modIndex) {
            const mod = this.getModules(rowIndex, colIndex)[modIndex];
            this.editingModule = JSON.parse(JSON.stringify(mod));
            if (!this.editingModule.json) this.editingModule.json = [];
            this.applyModuleSchemaDefaults();
            this.moduleConfigTab = 'content';
            this.customHtmlTreeExpanded = true;
            this.customHtmlDoubleHeight = false;
            this.customHtmlCollapsedStarts = [];
            this.customHtmlHistory = [this.getSchemaFieldEntry('body')?.value ?? ''];
            this.customHtmlFuture = [];
            this.editingModuleIndices = { rowIndex, colIndex, modIndex };
            this.showModuleConfigModal = true;
            this.$nextTick(() => this.ensureCustomHtmlCodeMirror());
        },

        toggleModuleActive(rowIndex, colIndex, modIndex) {
            const mod = this.getModules(rowIndex, colIndex)[modIndex];
            mod.active = mod.active === false;
            this.updateJson();
        },

        addModuleParam() {
            if (!this.editingModule.json) this.editingModule.json = [];
            this.editingModule.json.push({ name: '', value: '', _key: this.generateId() });
        },

        saveModuleConfig() {
            if (this.customHtmlCodeMirror) {
                this.setSchemaFieldValue('body', this.customHtmlCodeMirror.getValue());
            }
            const { rowIndex, colIndex, modIndex } = this.editingModuleIndices;
            const mod = this.getModules(rowIndex, colIndex)[modIndex];
            mod.json = this.editingModule.json
                .filter(p => (p.name || '').trim() !== '')
                .map(({ name, value }) => ({ name, value }));
            mod.active = this.editingModule.active !== false;
            this.showModuleConfigModal = false;
            this.updateJson();
        },

        // JSON operations
        getCleanRows() {
            return this.rows.map(row => {
                const clean = { ...row };
                delete clean._id;
                delete clean._expanded;
                clean.children = (row.children || []).map(group => {
                    return (Array.isArray(group) ? group : [group]).map(col => {
                        const cleanCol = { ...col };
                        delete cleanCol._id;
                        cleanCol.children = (col.children || []).map(modGroup => {
                            return (Array.isArray(modGroup) ? modGroup : [modGroup]).map(mod => {
                                const cleanMod = { ...mod };
                                delete cleanMod._id;
                                return cleanMod;
                            });
                        });
                        return cleanCol;
                    });
                });
                return clean;
            });
        },

        getJsonOutput() {
            return JSON.stringify(this.getCleanRows());
        },

        updateJson() {
            this.rawJson = JSON.stringify(this.getCleanRows(), null, 2);
        },

        toggleJsonView() {
            if (!this.showJson) {
                this.rawJson = JSON.stringify(this.getCleanRows(), null, 2);
            }
            this.showJson = !this.showJson;
        },

        parseRawJson() {
            try {
                const parsed = JSON.parse(this.rawJson);
                if (Array.isArray(parsed)) {
                    this.rows = parsed.map(row => this.normalizeRow(row));
                    this.jsonError = '';
                    this.$nextTick(() => this.initSortable());
                } else {
                    this.jsonError = 'JSON bir dizi (array) olmalıdır.';
                }
            } catch (e) {
                this.jsonError = 'Geçersiz JSON: ' + e.message;
            }
        },

        // SortableJS initialization
        initSortable() {
            // Row sorting
            const rowContainer = document.getElementById('layout-rows-container');
            if (rowContainer && !rowContainer._sortable) {
                rowContainer._sortable = new Sortable(rowContainer, {
                    handle: '.row-drag-handle',
                    animation: 150,
                    ghostClass: 'opacity-30',
                    onEnd: (evt) => {
                        // Find the actual template-generated elements (skip template tags)
                        const items = Array.from(rowContainer.children).filter(el => el.tagName !== 'TEMPLATE');
                        const movedRow = this.rows.splice(evt.oldIndex, 1)[0];
                        this.rows.splice(evt.newIndex, 0, movedRow);
                        this.updateJson();
                    }
                });
            }

            this.rows.forEach((row, rowIndex) => {
                const columnsContainer = document.getElementById('columns-' + row._id);
                if (columnsContainer && !columnsContainer._sortable && this.rows[rowIndex]?.children?.[0]) {
                    columnsContainer._sortable = new Sortable(columnsContainer, {
                        handle: '.col-drag-handle',
                        animation: 150,
                        ghostClass: 'opacity-30',
                        onEnd: (evt) => {
                            const group = this.rows[rowIndex].children[0];
                            const movedCol = group.splice(evt.oldIndex, 1)[0];
                            group.splice(evt.newIndex, 0, movedCol);
                            this.updateJson();
                        }
                    });
                }

                this.getColumns(rowIndex).forEach((col, colIndex) => {
                    const modulesContainer = document.getElementById('modules-' + col._id);
                    if (modulesContainer && !modulesContainer._sortable && this.getColumns(rowIndex)[colIndex]?.children?.[0]) {
                        modulesContainer._sortable = new Sortable(modulesContainer, {
                            handle: '.module-drag-handle',
                            animation: 150,
                            ghostClass: 'opacity-30',
                            draggable: '.module-sortable-item',
                            onEnd: (evt) => {
                                const group = this.getColumns(rowIndex)[colIndex].children[0];
                                const movedMod = group.splice(evt.oldIndex, 1)[0];
                                group.splice(evt.newIndex, 0, movedMod);
                                this.updateJson();
                            }
                        });
                    }
                });
            });
        }
    };
}
</script>
@endpush
