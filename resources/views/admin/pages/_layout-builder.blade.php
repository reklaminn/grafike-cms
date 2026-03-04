{{-- Visual Layout Builder - Alpine.js + SortableJS --}}
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
                    </div>

                    <div class="flex items-center gap-1">
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
                                 :style="'flex: 0 0 calc(' + (getColWidth(col) / 12 * 100) + '% - 0.5rem); max-width: calc(' + (getColWidth(col) / 12 * 100) + '% - 0.5rem);'">

                                {{-- Column Header --}}
                                <div class="flex items-center justify-between px-3 py-1.5 bg-gray-100 rounded-t-lg col-drag-handle cursor-grab">
                                    <div class="flex items-center gap-1">
                                        <i class="fas fa-grip-vertical text-gray-400 text-xs"></i>
                                        <span class="text-xs font-medium text-gray-600" x-text="col.coltype || 'col-12'"></span>
                                    </div>
                                    <div class="flex items-center gap-0.5">
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
                                        <div class="flex items-center justify-between px-2 py-1.5 bg-white border border-indigo-200 rounded text-xs module-drag-handle cursor-grab group">
                                            <div class="flex items-center gap-1.5 min-w-0">
                                                <i class="fas fa-grip-vertical text-gray-300"></i>
                                                <i class="fas fa-puzzle-piece text-indigo-400"></i>
                                                <span class="text-gray-700 truncate" x-text="getModuleName(mod.modulid)"></span>
                                            </div>
                                            <div class="flex items-center gap-0.5 opacity-0 group-hover:opacity-100 transition-opacity">
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
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 max-h-[80vh] overflow-y-auto">
            <div class="flex items-center justify-between px-6 py-4 border-b">
                <h3 class="text-base font-semibold text-gray-800">
                    Modül Yapılandırma: <span class="text-indigo-600" x-text="getModuleName(editingModule.modulid)"></span>
                </h3>
                <button type="button" @click="showModuleConfigModal = false" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-6 space-y-4">
                {{-- Dynamic config params --}}
                <div class="space-y-3">
                    <template x-for="(param, paramIndex) in editingModule.json || []" :key="paramIndex">
                        <div class="flex items-center gap-2">
                            <input type="text" x-model="param.name" placeholder="Parametre adı"
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            <input type="text" x-model="param.value" placeholder="Değer"
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            <button type="button" @click="editingModule.json.splice(paramIndex, 1)"
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
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
        moduleSearch: '',
        pickerTarget: {},
        _idCounter: 0,
        availableModules: @json(collect(config('modules'))->map(fn($m, $id) => ['id' => $id, 'name' => $m['name']])->values()),

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
            this.editingModuleIndices = { rowIndex, colIndex, modIndex };
            this.showModuleConfigModal = true;
        },

        addModuleParam() {
            if (!this.editingModule.json) this.editingModule.json = [];
            this.editingModule.json.push({ name: '', value: '' });
        },

        saveModuleConfig() {
            const { rowIndex, colIndex, modIndex } = this.editingModuleIndices;
            const mod = this.getModules(rowIndex, colIndex)[modIndex];
            mod.json = this.editingModule.json.filter(p => p.name.trim() !== '');
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
        }
    };
}
</script>
@endpush
