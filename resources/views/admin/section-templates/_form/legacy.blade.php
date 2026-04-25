<div class="rounded-xl border border-amber-200 bg-amber-50 p-6 shadow-sm">
    <h3 class="text-base font-semibold text-amber-900">Eski Builder Eşleme</h3>
    <p class="mt-2 text-sm text-amber-800">
        Eski <code>layout_json</code> modüllerini yeni Next.js builder picker'ında kullanılabilir block şablonlarına çevirmek için tutulur.
    </p>
    <div class="mt-4 space-y-4">
        <div>
            <label class="mb-1 block text-sm font-medium text-amber-900">Legacy Modül Anahtarı</label>
            <select name="legacy_module_key"
                    class="w-full rounded-lg border border-amber-300 bg-white px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-amber-500">
                <option value="">— Eşleme yok —</option>
                @foreach($legacyModuleOptions as $value => $label)
                    <option value="{{ $value }}" @selected(old('legacy_module_key', $sectionTemplate->legacy_module_key) === $value)>
                        {{ $label }}
                    </option>
                @endforeach
                @if(old('legacy_module_key', $sectionTemplate->legacy_module_key) && ! isset($legacyModuleOptions[old('legacy_module_key', $sectionTemplate->legacy_module_key)]))
                    <option value="{{ old('legacy_module_key', $sectionTemplate->legacy_module_key) }}" selected>
                        {{ old('legacy_module_key', $sectionTemplate->legacy_module_key) }} (kayıtlı)
                    </option>
                @endif
            </select>
            <p class="mt-1 text-xs text-amber-700">Eski builder modül sınıfından otomatik listeden seç.</p>
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-amber-900">Legacy Config Map JSON</label>
            <textarea name="legacy_config_map_json" rows="8"
                      class="w-full rounded-lg border border-amber-300 bg-white px-3 py-2 font-mono text-xs focus:border-transparent focus:ring-2 focus:ring-amber-500">{{ $legacyConfigMapValue }}</textarea>
            <p class="mt-1 text-xs text-amber-700">
                Örnek: <code>@json(['menu_id' => 'menu_id', 'show_breadcrumb' => 'show_breadcrumb'])</code>
            </p>
        </div>
    </div>
</div>
