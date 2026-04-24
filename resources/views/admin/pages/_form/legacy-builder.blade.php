<!-- Visual Layout Builder (Legacy) -->
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
    <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs text-emerald-800">
        Legacy ajans akışı için özel HTML eklemek istiyorsan <strong>Modül Ekle &gt; Custom HTML</strong> yolunu kullan.
        Bu modül eski builder içinde hızlı HTML section eklemek içindir; sonra istersen yeni builder'a dönüştürülebilir.
    </div>
    @include('admin.pages._layout-builder')
    <div class="mt-4 rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-xs text-blue-800">
        <div class="font-semibold">Bu alan eski sistem içindir.</div>
        <p class="mt-1 leading-5">
            <strong>Sayfa Düzeni (Layout Builder)</strong> eski Blade/CMS frontend’in <code>layout_json</code> yapısını düzenler.
            Eğer sayfa yeni Next.js demo akışında <code>sections_json</code> kullanıyorsa, burada boş görünmesi normaldir.
        </p>
    </div>
</div>
