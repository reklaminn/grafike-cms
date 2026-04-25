<div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
    <h3 class="text-base font-semibold text-gray-900">Geçiş Notu</h3>
    <ul class="mt-3 list-disc space-y-2 pl-5 text-sm text-gray-600">
        <li>Eski builder modülü için önce bir block şablonu oluştur.</li>
        <li><code>legacy_module_key</code> ile eski modülü bu kayda bağla.</li>
        <li>HTML modda hızlı geçiş yap, sonra aynı kaydı component moda taşı.</li>
        <li>Böylece eski modül picker mantığı yeni Next.js builder içinde tekrar kullanılabilir.</li>
    </ul>
</div>

<details class="rounded-xl border border-gray-200 bg-white shadow-sm">
    <summary class="cursor-pointer px-6 py-4 text-base font-semibold text-gray-900 hover:bg-gray-50">
        <i class="fas fa-circle-question mr-2 text-gray-400"></i>Alan Açıklamaları
    </summary>
    <div class="border-t border-gray-100 px-6 pb-5 pt-4 space-y-3 text-sm text-gray-600">
        <p><strong>Şablon Adı:</strong> Panelde göreceğin kullanıcı dostu isimdir.</p>
        <p><strong>Tema:</strong> Bu block şablonunun hangi tema ailesine ait olduğunu belirler.</p>
        <p><strong>Render Mode:</strong> <code>html</code> ise HTML Template çalışır. <code>component</code> ise Next.js component render eder.</p>
        <p><strong>Type:</strong> Hero, rich-text, article-list gibi ana block kategorisidir.</p>
        <p><strong>Variation:</strong> Aynı type içindeki tasarım varyantıdır. Örn: <code>porto-split</code>, <code>cards</code>.</p>
        <p><strong>Component Key:</strong> Sadece component modunda anlamlıdır. Next.js component anahtarı.</p>
        <p><strong>HTML Template:</strong> Ham HTML verip placeholder'a dönüştürebilir veya doğrudan placeholder'lı şablon yazabilirsin.</p>
        <p><strong>Schema Alanları:</strong> Builder formunda hangi alanların çıkacağını tanımlar.</p>
        <p><strong>Default Content JSON:</strong> Yeni block eklendiğinde ilk doldurulacak varsayılan değerlerdir.</p>
        <p><strong>Sistem Placeholder'ları:</strong> <code>site_name</code>, <code>phone</code>, <code>email</code>, <code>address</code> gibi alanlar panel ayarlarından gelir.</p>
        <p><strong>Menü Placeholder'ları:</strong> <code>menu_header_html</code>, <code>menu_footer_html</code> veya <code>menu_&lt;slug&gt;_html</code> kullanabilirsin.</p>
    </div>
</details>
