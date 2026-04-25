<div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
    <h3 class="text-base font-semibold text-gray-900">Temel Bilgiler</h3>
    <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
        <div class="md:col-span-2">
            <label class="mb-1 block text-sm font-medium text-gray-700">Şablon Adı *</label>
            <input type="text" name="name" required value="{{ old('name', $sectionTemplate->name) }}"
                   class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-gray-700">Tema *</label>
            <select id="theme_id_select" name="theme_id" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-indigo-500">
                <option value="">Tema seç</option>
                @foreach($themes as $theme)
                    <option value="{{ $theme->id }}" @selected($selectedThemeId === (string) $theme->id)>
                        {{ $theme->name }} ({{ $theme->slug }})
                    </option>
                @endforeach
            </select>
            <p class="mt-1 text-xs text-gray-500">Burada sadece sistemde kayıtlı temalar görünür.</p>
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-gray-700">Render Mode *</label>
            <select id="render_mode_select" name="render_mode" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-indigo-500">
                <option value="html" @selected(old('render_mode', $sectionTemplate->render_mode) === 'html')>html</option>
                <option value="component" @selected(old('render_mode', $sectionTemplate->render_mode) === 'component')>component</option>
            </select>
            <p class="mt-1 text-xs text-gray-500"><strong>html</strong>: HTML Template ile render edilir. <strong>component</strong>: Next.js component render eder.</p>
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-gray-700">Type *</label>
            <select id="type_select" name="type" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-indigo-500">
                <option value="">Type seç</option>
                @foreach($typeOptions as $value => $label)
                    <option value="{{ $value }}" @selected($selectedType === $value)>{{ $label }} ({{ $value }})</option>
                @endforeach
                <option value="__custom" @selected(! $selectedTypeIsKnown)>+ Yeni type oluştur</option>
            </select>
            <p class="mt-1 text-xs text-gray-500">Block'un ana kategorisidir.</p>
            <div id="type_custom_wrapper" class="mt-2 {{ $selectedTypeIsKnown ? 'hidden' : '' }}">
                <input type="text" id="type_custom_input" name="type_custom"
                       value="{{ $selectedTypeIsKnown ? '' : $selectedType }}"
                       placeholder="örn: pricing-table"
                       class="w-full rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-indigo-500">
                <p class="mt-1 text-xs text-indigo-700">Küçük harfli slug. Örn: <code>pricing-table</code>, <code>team-list</code>.</p>
            </div>
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-gray-700">Variation *</label>
            <div class="flex gap-2">
                <input type="text" id="variation_input" name="variation" list="variation_suggestions" required
                       value="{{ $selectedVariation }}" placeholder="variation yaz veya seç"
                       class="min-w-0 flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-indigo-500">
                <button type="button" id="normalize_variation_button"
                        class="shrink-0 rounded-lg bg-gray-100 px-3 py-2 text-xs font-medium text-gray-700 hover:bg-gray-200">Slug</button>
                <button type="button" id="suggest_variation_button"
                        class="shrink-0 rounded-lg bg-indigo-50 px-3 py-2 text-xs font-medium text-indigo-700 hover:bg-indigo-100">Yeni Öner</button>
            </div>
            <datalist id="variation_suggestions"></datalist>
            <div id="variation_status" class="mt-2 hidden rounded-lg px-3 py-2 text-xs"></div>
            <p class="mt-1 text-xs text-gray-500">Aynı type içindeki görünüm/stil farkı. Örn: <code>porto-split</code>, <code>cards</code>.</p>
        </div>
        <div id="component_key_wrapper">
            <label class="mb-1 block text-sm font-medium text-gray-700">Component Key</label>
            <input type="text" name="component_key" id="component_key_input"
                   value="{{ old('component_key', $sectionTemplate->component_key) }}"
                   placeholder="hero/porto-split"
                   list="component_key_suggestions"
                   class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-indigo-500">
            <datalist id="component_key_suggestions">
                @foreach($legacyModuleOptions as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </datalist>
            <p class="mt-1 text-xs text-gray-500">Sadece <code>component</code> modunda kullanılır. Next.js component anahtarıdır.</p>
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-gray-700">Önizleme Görseli</label>
            <input type="text" name="preview_image" value="{{ old('preview_image', $sectionTemplate->preview_image) }}"
                   placeholder="https://..."
                   class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-indigo-500">
        </div>
        <label class="flex items-center gap-2 rounded-lg bg-gray-50 px-3 py-2 md:col-span-2">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $sectionTemplate->is_active ?? true))
                   class="h-4 w-4 rounded border-gray-300 text-indigo-600">
            <span class="text-sm text-gray-700">Aktif</span>
        </label>
    </div>
</div>
