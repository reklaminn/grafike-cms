{{-- Block / Row / Column settings modals — lives inside frontendSectionEditor() Alpine scope --}}

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

                            <template x-if="(fieldSchema.type || 'text') === 'select'">
                                <select x-model="settingsBlock.content[fieldName]"
                                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200">
                                    <template x-for="option in (Array.isArray(fieldSchema.options) ? fieldSchema.options : [])" :key="option">
                                        <option :value="option" x-text="option"></option>
                                    </template>
                                </select>
                            </template>

                            <template x-if="!['textarea', 'number', 'boolean', 'select'].includes(fieldSchema.type || 'text')">
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

        <template x-if="columnSettingsDraft">
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
                    Yerleşim etiketi: <span class="font-semibold text-gray-800" x-text="columnLayoutSummary(columnSettingsDraft)"></span>
                </div>

                <div x-show="columnSettingsTab === 'layout'" class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-600">Genel (col-)</label>
                        <select
                                x-effect="$el.value = String(columnSettingsDraft.width ?? '')"
                                @change="columnSettingsDraft.width = $event.target.value; normalizeResponsive(columnSettingsDraft)"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                            <option value="">Yok</option>
                            <template x-for="width in [1,2,3,4,5,6,7,8,9,10,11,12]" :key="'base-' + width">
                                <option :value="String(width)" x-text="'col-' + width"></option>
                            </template>
                        </select>
                    </div>
                    <template x-for="breakpoint in ['sm', 'md', 'lg', 'xl']" :key="breakpoint">
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-600" x-text="breakpoint.toUpperCase() + ' (col-' + breakpoint + '-)'"></label>
                            <select
                                    x-effect="$el.value = String(columnSettingsDraft.responsive[breakpoint] ?? '')"
                                    @change="columnSettingsDraft.responsive[breakpoint] = $event.target.value"
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                                <option value="">Yok</option>
                                <template x-for="width in [1,2,3,4,5,6,7,8,9,10,11,12]" :key="breakpoint + '-' + width">
                                    <option :value="String(width)" x-text="'col-' + breakpoint + '-' + width"></option>
                                </template>
                            </select>
                        </div>
                    </template>
                    <label class="flex items-center gap-2 rounded-lg bg-gray-50 px-3 py-2 text-sm text-gray-700 sm:col-span-2">
                        <input type="checkbox" x-model="columnSettingsDraft.is_active" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        Kolon aktif
                    </label>
                </div>

                <div x-show="columnSettingsTab === 'style'" class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-600">CSS Sınıf</label>
                        <input type="text" x-model="columnSettingsDraft.css_class"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-600">Element ID</label>
                        <input type="text" x-model="columnSettingsDraft.element_id"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="mb-1 block text-xs font-medium text-gray-600">Inline Style</label>
                        <input type="text" x-model="columnSettingsDraft.inline_style"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="mb-1 block text-xs font-medium text-gray-600">Diğer Attributes</label>
                        <input type="text" x-model="columnSettingsDraft.custom_attributes"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                    </div>
                </div>

                <div x-show="columnSettingsTab === 'code'" class="space-y-4">
                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-600">Wrapper HTML</label>
                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-3 text-xs text-gray-700 font-mono overflow-x-auto"
                             x-text="columnPreview(columnSettingsDraft)"></div>
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
