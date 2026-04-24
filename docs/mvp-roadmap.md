# MVP Roadmap

## Karar Özeti

Bu proje için kesin başlangıç kararı:

- backend: Laravel CMS
- frontend: Next.js
- ilk teslim modeli: `Basic HTML Section Mode`
- ikinci ana evrim: `Structured Component Mode`
- uzun vadeli hedef: hibrit sistem
- editör omurgası: `Header / Body / Footer + Row / Column / Block`

Bu kararın sebebi:

- ajansın mevcut iş yapışını bozmamak
- Porto / Woodmart / benzeri HTML tema akışını ürünleştirmek
- ama bunu doğrudan Next.js tabanlı yeni mimariye oturtmak

## Faz 0: Temel Yön ve Hazırlık

Hedef:

- repo yapısını belirlemek
- veri modelini netleştirmek
- teknik kararları dondurmak

Çıktılar:

- mimari dokümanı
- başlangıç planı
- örnek theme pack
- örnek site template
- monorepo klasör yapısı

## Faz 1: Basic HTML Section Mode

Hedef:

- mevcut ajans akışını ürüne dönüştürmek
- HTML template section mantığını Next.js üzerinde çalıştırmak

Kapsam:

- Laravel public API kontratı
- Next.js app skeleton
- theme pack sistemi
- tema CSS/JS asset tanımı
- HTML section snippet modeli
- region tabanlı frontend editörü (`header/body/footer`)
- satır / kolon / block veri modeli
- sayfa bazlı block sıralama
- aktif/pasif row/column/block kontrolü
- sayfa bazlı custom css/js
- global custom css/js
- backend Sentry entegrasyon planı

İlk endpoint seti:

- `GET /api/site`
- `GET /api/settings`
- `GET /api/menus/{location}`
- `GET /api/pages/{slug}`
- `GET /api/articles/{slug}`
- `GET /api/articles`

İlk tema örneği:

- `porto-furniture`

İlk section seti:

- hero
- text-block
- testimonials
- faq
- cta-banner
- blog-list
- gallery
- video
- spacer

Başarı ölçütü:

- Porto benzeri bir temayı CSS/JS + HTML section parçalarıyla sisteme alabilmek
- bir firmaya uyarlayabilmek
- içerik/görsel/sıralama/custom css ile teslim edebilmek
- admin panelde `Header / Body / Footer` altında satır, kolon ve block mantığıyla düzenleme yapabilmek

## Faz 1.5: Section Template Olgunlaşması

Hedef:

- Faz 1'de gelen Basic HTML Section Mode'un içerik üretim döngüsünü kullanıcı için anlaşılır hâle getirmek
- "HTML yapıştır → block şablonuna çevir" akışını manuel JSON yazımı gerektirmeden kullanılabilir kılmak
- Şablon içine site/menu/ayar verilerini placeholder olarak gömmeyi tek tık ile yapılabilir hâle getirmek

Kapsam:

### HTML'yi Şablona Dönüştür — Repeat alan seçimi

- Mevcut otomatik DOM tarama algoritmasına interaktif seçim katmanı eklenir
- Yapıştırılan HTML üzerinde tekrar eden öbekler (ürün kartı, slide, list item) admin önizlemede işaretlenebilir
- Seçilen tekrar öbeği `slide_N_*` / `card_N_*` placeholder grubuna dönüştürülür ve schema'da repeater tipinde tek alan oluşturulur
- Kullanıcı tekrar sayısını (3, 4, 6 vb.) önizleme üzerinden belirleyebilir
- Repeat öbek sınırı görsel olarak çerçeve ile gösterilir; çoklu seçim yapılabilir

### Menü placeholder yönetimi

- HTML editörü içinden "Menü Ekle" butonu ile kayıtlı menülerden seçim yapılır
- Site içinde tanımlı menü slug/location listesi dropdown olarak sunulur
- Tam HTML (`{{{menu_<key>_html}}}`) mı yalnız item HTML (`{{{menu_<key>_items_html}}}`) mı seçimi yapılır
- Seçim sonrası placeholder doğru token ile editöre tek tıkta gömülür; manuel placeholder ezberi gerekmez
- Mevcut menülerin yenilenmesi için "menüleri yeniden yükle" butonu bulunur

### Site ayarlarından gelen sabit placeholder'lar

- Site bazlı genel alanlar (`phone`, `email`, `address`, `whatsapp_number`, `working_hours`, `tax_id` vb.) için placeholder picker
- HTML editöründen "Sistem Alanı Ekle" dropdown'u açılır, mevcut `site_name`, `theme_slug`, `logo_url`, `favicon_url`, `footer_text` gibi alanlar listelenir
- `SiteSetting` tablosuna yeni eklenen her alan otomatik placeholder kataloğuna düşer
- Kullanıcı her site için bu alanları admin → ayarlar üzerinden tek bir yerden doldurabilir; bütün block'lara otomatik yansır
- Eksik veya tanımsız placeholder'lar render anında uyarı verir

Başarı ölçütü:

- Yeni bir HTML temayı yapıştır + işaretle akışıyla 5 dakikada repeat alanlı block şablonuna dönüştürebilmek
- Menü ve sistem placeholder'larını manuel yazmadan tek tıkla kullanabilmek
- Site bazında sabit alanları tek noktada düzenleyip tüm bloklara yansıtabilmek

### Block Şablon Yönetim Paneli — P1 (Hızlı Kazanımlar)

`/admin/section-templates` ekranında ilk dalgada uygulanması gereken iyileştirmeler. Hepsi 30–90 dakikalık küçük işlerdir, tek commit/PR seti olarak gönderilebilir.

- **Index'te preview thumbnail göster** — `preview_image` varsa kart tepesine 16:9 görsel, yoksa type ikonunu (hero/footer/cta) yerleştir
- **Index'te kullanım sayısı badge'i** — bir şablon kaç sayfa tarafından kullanılıyor; silmeden önce etkiyi gösterir
- **Status filter butonları** — "Tümü / Aktif / Pasif" pill toggle, mevcut tema + arama filtresinin yanına eklenir
- **Klonla aksiyonu** — `duplicate(SectionTemplate $st)` controller method'u; yeni varyasyon yaratırken kopya-yapıştır yükünü kaldırır
- **JSON form-level validation** — `SectionTemplateRequest::withValidator()` hook'u ile `schema_json`, `default_content_json`, `legacy_config_map_json` decode edilemiyorsa hata; her schema alanının `type` zorunlu
- **Render mode'a göre alan görünürlüğü netleşsin** — html mode'da `component_key` gizli (mevcut), component mode'da `html_template` collapsed-default
- **Form partial parçalama** — `_form.blade.php` (552 satır) → `_form/basic-info`, `_form/template`, `_form/schema`, `_form/legacy`, `_form/sidebar/docs` partial'larına bölünür (page form refactor pattern'i ile aynı yaklaşım)

### Block Şablon Yönetim Paneli — P2 (Orta Vadeli Geliştirmeler)

İkinci dalgada uygulanacak orta ölçekli geliştirmeler. Her biri yarım gün ile 1 gün arası iş yükü taşır.

- **Schema visual builder** — JSON yerine "Alan Ekle" butonu + her alan için `key/type/label/required/max/min/options/help` form satırı; arka planda JSON üretilir. Desteklenen tipler: `string | text | textarea | url | email | number | boolean | enum | media_id | color | select | repeater`
- **Default content otomatik türetme** — schema yapısından default JSON üret butonu (key→type/label'a göre placeholder); textarea readonly olur, "düzenle" tıklanınca free-edit
- **HTML ↔ Schema key diff göstergesi** — HTML'deki tüm `{{key}}` taranır; schema'da olmayanlar kırmızı, schema'da olup HTML'de olmayanlar gri liste; kayıttan önce uyarı verir
- **CodeMirror / Monaco entegrasyonu** — `html_template` için HTML highlighting + basic linting; satır numarası + auto-indent
- **HTML'yi Şablona Dönüştür — güvenli mod** — modal: "Mevcut schema'yı sıfırlamak istiyor musun? Veya birleştir mi?" — sessiz overwrite riskini kaldırır
- **Live preview iframe** — form altında küçük bir iframe; "Önizle" butonu schema + default_content ile geçici render endpoint'ine POST atar (legacy `ModuleRenderer` / `LayoutRenderer` ile aynı şablonu HTML alır), iframe gösterir

### Block Şablon Yönetim Paneli — P3 (Yapısal Geliştirmeler)

Üçüncü dalgada uygulanacak yapısal değişiklikler. Veri modeli ve sistem mimarisini etkileyen, daha uzun vadeli iyileştirmelerdir.

- **Soft delete** — `section_templates` tablosuna `deleted_at` kolonu eklenir. Bir şablon silinmek istendiğinde halen kullanan sayfalar listelenir; kullanıcı "arşivle" veya "zorla sil" seçimi yapar. Yanlışlıkla silinen şablonu geri alma imkânı doğar.
- **`preview_image` Spatie media-library upload** — text URL alanı yerine. Diğer tüm admin formlarındaki upload pattern ile tutarlı; thumbnail otomatik üretimi ve responsive görsel desteği gelir.
- **Component key autocomplete kaynağı** — `apps/frontend/components/sections/` dizinindeki `.tsx` dosyaları build-time taranıp `storage/app/component-registry.json` manifest'i üretilir. Admin form bu manifest'i okuyup component key alanına autocomplete sunar; tipo ile bozuk kayıt riski biter.
- **Section template versiyonlama** — Page revisions pattern'i (`page_revisions`) section template'lerine taşınır. Production'da yayında olan kritik şablon için "v1 yayında, v2 draft" akışı; canlı şablonu kırmadan deneme yapılabilir, geri yükleme tek tıktır.
- **`legacy_module_key` enum select** — `app/Services/ModuleRenderer/Modules/*.php` dosyaları taranıp legacy modül kataloğu otomatik üretilir; plain text input yerine select dropdown kullanılır. Eşleme hatası riski kalkar.

## Faz 2: Structured Component Mode

Hedef:

- en çok kullanılan section'ları HTML snippet'ten çıkarıp reusable Next.js component'e çevirmek

Kapsam:

- section registry
- section schema standardı
- variation sistemi
- theme token sistemi
- React tabanlı section renderer
- hero/testimonials/blog-list/cta gibi çekirdek section'ların component sürümleri
- frontend Sentry entegrasyonu

Not:

- Structured mode yeni bir ikinci editör yaratmayacak
- Faz 1'de gelen region tabanlı builder korunacak
- yalnızca bazı block'lar `render_mode=component` ile render edilecek

Başarı ölçütü:

- aynı page içinde hem HTML section hem component section kullanılabilmesi
- en sık kullanılan blokların structured render'a taşınması

## Faz 3: Template ve Site Instance Sistemi

Hedef:

- oluşturulan siteleri tekrar kullanılabilir template varlığına dönüştürmek

Kapsam:

- site instance modeli
- siteyi template olarak kaydetme
- template'ten yeni site oluşturma
- theme preset kopyalama
- ajans/müşteri yetki sınırları

Başarı ölçütü:

- bir template'in birden fazla firmada renk/section/varyasyon farkıyla kullanılabilmesi

## Faz 4: AI-Assisted Template Creation

Hedef:

- referans ekran görüntülerinden reusable theme/section/template üretimini hızlandırmak

Kapsam:

- referans görsel analizi
- section extraction
- theme preset önerisi
- variation önerisi
- template taslağı üretimi

Başarı ölçütü:

- Porto / Woodmart / özel referanslardan daha hızlı template üretmek

## Faz 5: Olgunlaşma

Hedef:

- üretim güvenliği ve panel deneyimini olgunlaştırmak

Kapsam:

- preview/versioning
- reusable block marketplace
- theme diff / override takibi
- daha net müşteri/ajans erişim modeli
- taşınabilir template export/import

## Teknik Sıralama

Kod geliştirme sırası şu olmalı:

1. Next.js temel app
2. Laravel public API
3. Basic HTML Section Mode
4. Region / row / column / block editörü
5. Theme packs
6. Structured component renderer
7. Template/site instance sistemi
8. AI hızlandırıcı katman

## Bu Roadmap Nasıl Kullanılacak

Bu dosya sabit bir sözleşme değildir.

Ama şu kararı sabit kabul eder:

- ilk ürün teslim yaklaşımı `Basic HTML Section Mode`
- hedef platform `Next.js + Laravel`

İleride fazlar:

- birleştirilebilir
- bölünebilir
- yeniden sıralanabilir

ancak temel başlangıç yönü korunmalıdır.
