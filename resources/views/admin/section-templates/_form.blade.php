@php
    $schemaValue = old('schema_json', json_encode($sectionTemplate->schema_json ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    $defaultContentValue = old('default_content_json', json_encode($sectionTemplate->default_content_json ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    $legacyConfigMapValue = old('legacy_config_map_json', json_encode($sectionTemplate->legacy_config_map_json ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    $selectedThemeId = (string) old('theme_id', $sectionTemplate->theme_id);
    $selectedType = old('type', $sectionTemplate->type);
    $selectedVariation = old('variation', $sectionTemplate->variation);
@endphp

<div class="grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,1fr)_380px]">
    <div class="space-y-6">
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
                    <p class="mt-1 text-xs text-gray-500">Burada sadece sistemde kayıtlı temalar görünür. Tek tema görüyorsan, local veritabanında şu an tek theme kaydı var demektir.</p>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Render Mode *</label>
                    <select id="render_mode_select" name="render_mode" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-indigo-500">
                        <option value="html" @selected(old('render_mode', $sectionTemplate->render_mode) === 'html')>html</option>
                        <option value="component" @selected(old('render_mode', $sectionTemplate->render_mode) === 'component')>component</option>
                    </select>
                    <p class="mt-1 text-xs text-gray-500"><strong>html</strong>: şablon doğrudan HTML Template ile render edilir. <strong>component</strong>: Next.js tarafında bir component ile render edilir.</p>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Type *</label>
                    <select id="type_select" name="type" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-indigo-500">
                        <option value="">Type seç</option>
                        @foreach($typeOptions as $value => $label)
                            <option value="{{ $value }}" @selected($selectedType === $value)>{{ $label }} ({{ $value }})</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500">Type, block'un ana kategorisidir. Aynı type içinde farklı variation kayıtları olabilir.</p>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Variation *</label>
                    <input type="text"
                           id="variation_input"
                           name="variation"
                           list="variation_suggestions"
                           required
                           value="{{ $selectedVariation }}"
                           placeholder="variation yaz veya aşağıdan seç"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-indigo-500">
                    <datalist id="variation_suggestions"></datalist>
                    <p class="mt-1 text-xs text-gray-500">Variation, aynı type içindeki görünüm/stil farkını temsil eder. İstersen mevcut önerilerden seç, istersen yeni bir variation değeri yaz.</p>
                </div>
                <div id="component_key_wrapper">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Component Key</label>
                    <input type="text" name="component_key" value="{{ old('component_key', $sectionTemplate->component_key) }}"
                           placeholder="hero/porto-split"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-indigo-500">
                    <p class="mt-1 text-xs text-gray-500">Sadece <code>component</code> modunda kullanılır. Next.js tarafında hangi component'in çalışacağını belirtir.</p>
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

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h3 class="text-base font-semibold text-gray-900">HTML / Component İçeriği</h3>
            <div class="mt-4 space-y-4">
                <div>
                    <div class="mb-1 flex items-center justify-between gap-3">
                        <label class="block text-sm font-medium text-gray-700">HTML Template</label>
                        <button type="button"
                                id="generate_from_template"
                                class="inline-flex items-center gap-2 rounded-lg bg-indigo-50 px-3 py-1.5 text-xs font-medium text-indigo-700 hover:bg-indigo-100">
                            <i class="fas fa-wand-magic-sparkles"></i>
                            HTML'yi Şablona Dönüştür
                        </button>
                    </div>
                    <textarea id="html_template_input" name="html_template" rows="14"
                              class="w-full rounded-lg border border-gray-300 px-3 py-2 font-mono text-xs focus:border-transparent focus:ring-2 focus:ring-indigo-500">{{ old('html_template', $sectionTemplate->html_template) }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">Ham HTML yapıştırabilir veya mevcut placeholder'lı template kullanabilirsin. Buton metin, link, görsel ve background-image alanlarını placeholder'a çevirir.</p>
                    <div class="mt-3 rounded-lg border border-blue-200 bg-blue-50 px-3 py-3 text-xs text-blue-900">
                        <strong>Placeholder kuralı:</strong>
                        <div class="mt-2 space-y-1">
                            <div><code>@{{title}}</code>, <code>@{{button_text}}</code>: düz alan</div>
                            <div><code>@{{{body_html}}}</code>, <code>@{{items_html}}</code>: HTML / repeat çıktı alanı</div>
                            <div><code>@{{image_url}}</code>, <code>@{{hero_image_url}}</code>, <code>@{{logo_url}}</code>: görsel URL alanı</div>
                            <div><code>@{{image_alt}}</code>: görsel alt metni</div>
                            <div><code>@{{slide_1_title}}</code>, <code>@{{slide_2_image_url}}</code>: tekrar eden bloklar için indeksli alan</div>
                            <div><code>@{{site_name}}</code>, <code>@{{theme_slug}}</code>, <code>@{{phone}}</code>, <code>@{{email}}</code>, <code>@{{address}}</code>: sistemden gelen hazır alanlar</div>
                            <div><code>@{{whatsapp_number}}</code>, <code>@{{footer_text}}</code>, <code>@{{logo_url}}</code>, <code>@{{favicon_url}}</code>: ayarlar/sosyal alanları</div>
                            <div><code>@{{{menu_header_html}}}</code>, <code>@{{{menu_footer_html}}}</code>: ilgili menüyü tam HTML olarak getirir</div>
                            <div><code>@{{{menu_header_items_html}}}</code>: sadece menü item HTML'ini getirir, kendi <code>&lt;ul&gt;</code> yapını sen kurarsın</div>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Schema JSON</label>
                    <textarea id="schema_json_input" name="schema_json" rows="12"
                              class="w-full rounded-lg border border-gray-300 px-3 py-2 font-mono text-xs focus:border-transparent focus:ring-2 focus:ring-indigo-500">{{ $schemaValue }}</textarea>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Default Content JSON</label>
                    <textarea id="default_content_json_input" name="default_content_json" rows="12"
                              class="w-full rounded-lg border border-gray-300 px-3 py-2 font-mono text-xs focus:border-transparent focus:ring-2 focus:ring-indigo-500">{{ $defaultContentValue }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-6 shadow-sm">
            <h3 class="text-base font-semibold text-amber-900">Eski Builder Eşleme</h3>
            <p class="mt-2 text-sm text-amber-800">
                Buradaki alanlar, eski <code>layout_json</code> modüllerini yeni Next.js builder picker'ında kullanılabilir block şablonlarına çevirmek için tutulur.
            </p>
            <div class="mt-4 space-y-4">
                <div>
                    <label class="mb-1 block text-sm font-medium text-amber-900">Legacy Modül Anahtarı</label>
                    <input type="text" name="legacy_module_key" value="{{ old('legacy_module_key', $sectionTemplate->legacy_module_key) }}"
                           placeholder="TopMenu, FullContent, ContentBlock"
                           class="w-full rounded-lg border border-amber-300 bg-white px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-amber-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-amber-900">Legacy Config Map JSON</label>
                    <textarea name="legacy_config_map_json" rows="10"
                              class="w-full rounded-lg border border-amber-300 bg-white px-3 py-2 font-mono text-xs focus:border-transparent focus:ring-2 focus:ring-amber-500">{{ $legacyConfigMapValue }}</textarea>
                    <p class="mt-1 text-xs text-amber-700">
                        Örnek: <code>@json(['menu_id' => 'menu_id', 'show_breadcrumb' => 'show_breadcrumb'])</code>
                    </p>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h3 class="text-base font-semibold text-gray-900">Geçiş Notu</h3>
            <ul class="mt-3 list-disc space-y-2 pl-5 text-sm text-gray-600">
                <li>Eski builder modülü için önce bir block şablonu oluştur.</li>
                <li><code>legacy_module_key</code> ile eski modülü bu kayda bağla.</li>
                <li>HTML modda hızlı geçiş yap, sonra aynı kaydı component moda taşı.</li>
                <li>Böylece eski modül picker mantığı yeni Next.js builder içinde tekrar kullanılabilir.</li>
            </ul>
        </div>

        <div class="rounded-xl border border-sky-200 bg-sky-50 p-6 shadow-sm">
            <h3 class="text-base font-semibold text-sky-900">Alan Açıklamaları</h3>
            <div class="mt-3 space-y-3 text-sm text-sky-900">
                <p><strong>Şablon Adı:</strong> Panelde göreceğin kullanıcı dostu isimdir.</p>
                <p><strong>Tema:</strong> Bu block şablonunun hangi tema ailesine ait olduğunu belirler. Farklı temalar için ayrı varyasyonlar tutulabilir.</p>
                <p><strong>Render Mode:</strong> <code>html</code> ise doğrudan HTML Template çalışır. <code>component</code> ise Next.js component render eder.</p>
                <p><strong>Type:</strong> Hero, rich-text, article-list gibi ana block kategorisidir.</p>
                <p><strong>Variation:</strong> Aynı type içindeki tasarım varyantıdır. Örn: <code>porto-split</code>, <code>cards</code>, <code>minimal</code>.</p>
                <p><strong>Component Key:</strong> Sadece component modunda anlamlıdır. Next.js tarafındaki component anahtarını belirtir.</p>
                <p><strong>HTML Template:</strong> Faz 1'de ana üretim alanıdır. Ham HTML verip placeholder'a dönüştürebilir veya doğrudan placeholder'lı şablon yazabilirsin.</p>
                <p><strong>Schema JSON:</strong> Builder formunda hangi alanların çıkacağını tanımlar.</p>
                <p><strong>Default Content JSON:</strong> Yeni block eklendiğinde ilk doldurulacak varsayılan değerlerdir.</p>
                <p><strong>Hazır Sistem Placeholder'ları:</strong> <code>site_name</code>, <code>theme_slug</code>, <code>phone</code>, <code>email</code>, <code>address</code>, <code>whatsapp_number</code>, <code>footer_text</code>, <code>logo_url</code>, <code>favicon_url</code> gibi alanlar panel ayarlarından gelir.</p>
                <p><strong>Menü Placeholder'ları:</strong> <code>menu_header_html</code>, <code>menu_footer_html</code> veya bir menünün slug/location değerine göre <code>menu_&lt;anahtar&gt;_html</code> ve <code>menu_&lt;anahtar&gt;_items_html</code> kullanabilirsin.</p>
                <p><strong>Legacy Modül Anahtarı:</strong> Eski builder modülünü bu yeni block şablonuna bağlamak için kullanılır.</p>
                <p><strong>Legacy Config Map JSON:</strong> Eski modüldeki parametrelerin yeni content alanlarına nasıl aktarılacağını tanımlar.</p>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-indigo-700">
                <i class="fas fa-save"></i> Kaydet
            </button>
            <a href="{{ route('admin.section-templates.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-100 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-200">
                İptal
            </a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const variationOptions = @json($variationOptions);
    const initialVariation = @json($selectedVariation);
    const themeSelect = document.getElementById('theme_id_select');
    const typeSelect = document.getElementById('type_select');
    const variationInput = document.getElementById('variation_input');
    const variationSuggestions = document.getElementById('variation_suggestions');
    const renderModeSelect = document.getElementById('render_mode_select');
    const componentKeyWrapper = document.getElementById('component_key_wrapper');
    const htmlTemplateInput = document.getElementById('html_template_input');
    const schemaInput = document.getElementById('schema_json_input');
    const defaultContentInput = document.getElementById('default_content_json_input');
    const generateButton = document.getElementById('generate_from_template');

    if (!htmlTemplateInput || !schemaInput || !defaultContentInput || !generateButton || !themeSelect || !typeSelect || !variationInput || !variationSuggestions || !renderModeSelect || !componentKeyWrapper) {
        return;
    }

    const updateVariationOptions = () => {
        const themeId = themeSelect.value;
        const type = typeSelect.value;
        const currentValue = variationInput.value || initialVariation || '';
        const options = (variationOptions?.[themeId]?.[type]) || [];

        variationSuggestions.innerHTML = '';

        const seen = new Set();
        [...options, currentValue].filter(Boolean).forEach((variation) => {
            if (seen.has(variation)) {
                return;
            }

            seen.add(variation);
            const option = document.createElement('option');
            option.value = variation;
            variationSuggestions.appendChild(option);
        });
    };

    const updateRenderModeUi = () => {
        componentKeyWrapper.style.display = renderModeSelect.value === 'component' ? '' : 'none';
    };

    themeSelect.addEventListener('change', updateVariationOptions);
    typeSelect.addEventListener('change', updateVariationOptions);
    renderModeSelect.addEventListener('change', updateRenderModeUi);

    updateVariationOptions();
    updateRenderModeUi();

    const openBraces = '{' + '{';
    const closeBraces = '}' + '}';
    const placeholderRegex = new RegExp(`${openBraces}{?\\s*([a-zA-Z0-9_]+)\\s*}?${closeBraces}`, 'g');

    const labelize = (key) => key
        .replace(/_html$/i, '')
        .replace(/_url$/i, ' url')
        .replace(/_alt$/i, ' alt')
        .replace(/_/g, ' ')
        .replace(/\b\w/g, (char) => char.toUpperCase());

    const inferType = (key) => {
        if (/_html$/i.test(key)) {
            return 'textarea';
        }

        if (/(^|_)(image|img|photo|logo|icon|avatar|banner|thumbnail|cover|background)(_|$)/i.test(key)) {
            if (/_alt$/i.test(key)) {
                return 'text';
            }

            return 'image';
        }

        if (/^(show_|is_|has_)/i.test(key)) {
            return 'boolean';
        }

        if (/(count|limit|height|width|columns|order|sort)/i.test(key)) {
            return 'number';
        }

        if (/(body|description|subtitle|content|excerpt|caption|message|summary|text)/i.test(key)) {
            return 'textarea';
        }

        return 'text';
    };

    const inferDefaultValue = (key, type) => {
        if (type === 'boolean') {
            return false;
        }

        if (type === 'number') {
            return 0;
        }

        if (type === 'image') {
            return `https://placehold.co/1200x800?text=${encodeURIComponent(labelize(key))}`;
        }

        if (/_html$/i.test(key)) {
            if (/(items|cards|features|slides|logos|gallery|list)/i.test(key)) {
                return '<div class="item-card">Tekrarlı alan örneği</div>';
            }

            return '<p>İçerik buraya gelecek.</p>';
        }

        if (/_url$/i.test(key)) {
            return '#';
        }

        if (/_alt$/i.test(key)) {
            return 'Görsel açıklaması';
        }

        if (/(title|heading|name)/i.test(key)) {
            return 'Örnek Başlık';
        }

        if (/(description|subtitle|excerpt|caption|text|body|content|message|summary)/i.test(key)) {
            return 'Örnek içerik';
        }

        return '';
    };

    const hasPlaceholders = (template) => placeholderRegex.test(template);

    const inferTextBaseKey = (element, text) => {
        const tag = (element?.tagName || '').toLowerCase();
        const normalized = text.trim().toLowerCase();

        if (tag === 'a' || tag === 'button') {
            return 'button_text';
        }

        if (tag === 'h1' || tag === 'h2' || tag === 'h3') {
            return 'title';
        }

        if (tag === 'h4' || tag === 'h5' || tag === 'h6') {
            return 'eyebrow';
        }

        if (tag === 'p') {
            if (normalized.length > 120) {
                return 'body_html';
            }

            return normalized.length > 60 ? 'description' : 'subtitle';
        }

        if (tag === 'img') {
            return 'image_alt';
        }

        if (tag === 'li') {
            return 'item_text';
        }

        return 'text';
    };

    const inferRepeatPrefix = (element) => {
        let current = element;

        while (current && current.parentElement) {
            const parent = current.parentElement;
            const siblings = Array.from(parent.children).filter((child) => {
                return child.tagName === current.tagName && child.className === current.className;
            });

            if (siblings.length > 1) {
                const index = siblings.indexOf(current) + 1;
                const className = current.className || '';
                const base = /slide|owl-item/i.test(className)
                    ? 'slide'
                    : /card|item/i.test(className)
                        ? 'item'
                        : 'group';

                return `${base}_${index}_`;
            }

            current = parent;
        }

        return '';
    };

    const nextUniqueKey = (baseKey, schema) => {
        if (!schema[baseKey]) {
            return baseKey;
        }

        let index = 2;
        while (schema[`${baseKey}_${index}`]) {
            index += 1;
        }

        return `${baseKey}_${index}`;
    };

    const createPlaceholderToken = (key, raw = false) => {
        if (raw) {
            return `${openBraces}{${key}}${closeBraces}`;
        }

        return `${openBraces}${key}${closeBraces}`;
    };

    const buildFromPlaceholders = (placeholders) => {
        const schema = {};
        const defaults = {};

        placeholders.forEach((key) => {
            const type = inferType(key);
            schema[key] = {
                type,
                label: labelize(key),
            };
            defaults[key] = inferDefaultValue(key, type);
        });

        return { schema, defaults };
    };

    const transformRawHtmlToTemplate = (template) => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(`<div id="template-root">${template}</div>`, 'text/html');
        const root = doc.getElementById('template-root');

        if (!root) {
            return null;
        }

        const schema = {};
        const defaults = {};

        const registerField = (baseKey, type, defaultValue, raw = false) => {
            const key = nextUniqueKey(baseKey, schema);
            schema[key] = {
                type,
                label: labelize(key),
            };
            defaults[key] = defaultValue;

            return createPlaceholderToken(key, raw);
        };

        root.querySelectorAll('*').forEach((element) => {
            if (element.tagName === 'SCRIPT' || element.tagName === 'STYLE') {
                return;
            }

            const repeatPrefix = inferRepeatPrefix(element);

            if (element.hasAttribute('href')) {
                const baseKey = `${repeatPrefix}${element.tagName.toLowerCase() === 'a' ? 'button_url' : 'link_url'}`;
                element.setAttribute('href', registerField(baseKey, 'text', element.getAttribute('href') || '#'));
            }

            if (element.hasAttribute('src')) {
                const tag = element.tagName.toLowerCase();
                const baseKey = `${repeatPrefix}${tag === 'img' ? 'image_url' : 'media_url'}`;
                element.setAttribute('src', registerField(baseKey, 'image', element.getAttribute('src') || inferDefaultValue(baseKey, 'image')));
            }

            if (element.hasAttribute('alt')) {
                const baseKey = `${repeatPrefix}image_alt`;
                element.setAttribute('alt', registerField(baseKey, 'text', element.getAttribute('alt') || 'Görsel açıklaması'));
            }

            const styleValue = element.getAttribute('style') || '';
            if (/background-image\s*:\s*url\(/i.test(styleValue)) {
                const match = styleValue.match(/background-image\s*:\s*url\((['"]?)(.*?)\1\)/i);
                if (match && match[2]) {
                    const token = registerField(`${repeatPrefix}background_image_url`, 'image', match[2]);
                    element.setAttribute(
                        'style',
                        styleValue.replace(match[2], token)
                    );
                }
            }
        });

        const walker = doc.createTreeWalker(root, NodeFilter.SHOW_TEXT);
        const textNodes = [];

        while (walker.nextNode()) {
            textNodes.push(walker.currentNode);
        }

        textNodes.forEach((node) => {
            const rawText = node.nodeValue || '';
            const trimmed = rawText.trim();
            const parent = node.parentElement;

            if (!parent || trimmed === '' || parent.tagName === 'SCRIPT' || parent.tagName === 'STYLE') {
                return;
            }

            const repeatPrefix = inferRepeatPrefix(parent);
            const baseKey = `${repeatPrefix}${inferTextBaseKey(parent, trimmed)}`;
            const type = inferType(baseKey);
            const token = registerField(baseKey, type, trimmed, /_html$/i.test(baseKey));

            node.nodeValue = rawText.replace(trimmed, token);
        });

        return {
            template: root.innerHTML.trim(),
            schema,
            defaults,
        };
    };

    generateButton.addEventListener('click', () => {
        const template = htmlTemplateInput.value || '';
        placeholderRegex.lastIndex = 0;

        if (hasPlaceholders(template)) {
            const placeholders = [];
            const seen = new Set();

            let match;
            while ((match = placeholderRegex.exec(template)) !== null) {
                const key = match[1];

                if (!seen.has(key)) {
                    seen.add(key);
                    placeholders.push(key);
                }
            }

            const { schema, defaults } = buildFromPlaceholders(placeholders);
            schemaInput.value = JSON.stringify(schema, null, 2);
            defaultContentInput.value = JSON.stringify(defaults, null, 2);
            return;
        }

        const transformed = transformRawHtmlToTemplate(template);

        if (!transformed) {
            window.alert('HTML dönüştürülemedi.');
            return;
        }

        htmlTemplateInput.value = transformed.template;
        schemaInput.value = JSON.stringify(transformed.schema, null, 2);
        defaultContentInput.value = JSON.stringify(transformed.defaults, null, 2);
    });
});
</script>
