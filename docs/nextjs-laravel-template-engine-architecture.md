# Next.js Frontend + Laravel CMS Template Engine

## Amaç

Bu doküman, mevcut Laravel CMS projesini ajans ölçeğinde tekrar kullanılabilir bir
tema/template motoruna dönüştürmek için önerilen mimariyi tanımlar.

Hedef model:

- Laravel: admin panel, içerik yönetimi, medya, SEO, ayarlar, tema/template metadata
- Next.js: public frontend renderer
- AI araçları: yeni tema/section/template üretimi için hızlandırıcı katman
- Her yayınlanan site: tekrar kullanılabilir template veya site instance olarak saklanabilir

Bu sayede:

- Aynı template birden fazla firmada tekrar kullanılabilir
- Renkler, section sırası, varyasyonlar ve içerik değiştirilebilir
- Porto / Woodmart / özel referans tasarımlar preset haline getirilebilir
- Müşteri içerik ekler, ajans görsel dili ve varyasyonları yönetir

## Bugünkü Çalışma Biçiminin Ürünleşmiş Hali

Bugünkü fiili yöntem:

- Bir HTML tema seçiliyor
- Tema CSS/JS dosyaları projeye ekleniyor
- Sayfa HTML'i section'lara ayrılıyor
- Section'lar CMS içinde içerik gibi yönetiliyor
- Küçük farklılıklar custom CSS/JS ile kapatılıyor

Önerilen sistem, bunun daha düzenli ve tekrar kullanılabilir sürümüdür:

- Theme Pack
- Section Library
- Template
- Site Instance
- Theme Tokens
- Controlled Custom CSS/JS

## Ana Kavramlar

### 1. Theme Pack

Tema paketi, bir tasarım ailesinin varlıklarını ve görsel kurallarını taşır.

Örnekler:

- `porto-furniture`
- `porto-corporate`
- `woodmart-fashion`
- `woodmart-shop`

Theme pack içinde:

- CSS asset'leri
- JS asset'leri
- fontlar
- vendor bağımlılıkları
- header/footer varyasyonları
- temel renk ve tipografi presetleri

Theme pack, "bu site hangi görsel aileye ait?" sorusunun cevabıdır.

### 2. Section Library

Section library, her tema için kullanılabilen içerik bloklarını tutar.

Örnek section tipleri:

- hero
- text-block
- testimonials
- faq
- feature-cards
- logo-cloud
- team
- gallery
- cta
- blog-list
- product-grid
- lookbook
- video

Bir section sadece tip değil, aynı zamanda varyasyon da taşır:

- `hero.porto-split`
- `hero.woodmart-centered`
- `blog-list.cards-3col`
- `blog-list.magazine`
- `testimonials.carousel`
- `testimonials.grid`

### 3. Template

Template, tema üstüne kurulmuş hazır sayfa kurgusudur.

Örnek:

- `porto-furniture-home`
- `porto-furniture-about`
- `woodmart-fashion-home`
- `woodmart-fashion-blog-index`

Bir template şunları tanımlar:

- hangi theme pack'i kullanıyor
- hangi section'lar var
- section sırası
- default varyasyonlar
- default içerik alanları
- header/footer tipi

### 4. Site Instance

Site instance, bir firmaya ait gerçek site kurulumudur.

Örnek:

- `firma-a.com`
- `firma-b.com`

Bir instance:

- bir theme pack kullanır
- bir veya daha fazla template'ten türetilir
- kendi renk token'larına sahiptir
- kendi içeriklerine sahiptir
- kendi özel CSS/JS override'larına sahip olabilir

Bu sayede aynı template iki firmada farklı görünür:

- renkler değişir
- görseller değişir
- bazı section'lar kapanır
- varyasyon seçimi değişir

## Roller ve Yönetim Sınırları

### Müşteri Yetkisi

Müşterinin panelde yapabilmesi gerekenler:

- yeni testimonial eklemek
- ekip kartı eklemek
- SSS eklemek
- CTA metinlerini değiştirmek
- blog liste varyasyonu seçmek
- sayfa içindeki section'ları aktif/pasif yapmak
- section sırasını değiştirmek

Müşterinin yapmaması gerekenler:

- global CSS'e sınırsız müdahale
- grid sistemini bozma
- tasarım altyapısını değiştirme
- tema asset'lerini değiştirme

### Ajans Yetkisi

Ajans panelden şunları yönetebilmelidir:

- aktif tema seçimi
- global color tokens
- typography scale
- button / card / radius presetleri
- header/footer varyasyonları
- blog list/detail varyasyonları
- özel CSS/JS override
- yeni section template yayınlama

## Önerilen Veri Modeli

### themes

Tema ailesi kayıtları.

Alanlar:

- `id`
- `name`
- `slug`
- `engine` (`next`)
- `assets_json`
- `tokens_json`
- `settings_schema_json`
- `is_active`

### section_templates

Sistemde kullanılabilen section tanımları.

Alanlar:

- `id`
- `theme_id`
- `type`
- `variation`
- `name`
- `component_key`
- `schema_json`
- `preview_image`
- `is_active`

### page_templates

Hazır sayfa şablonları.

Alanlar:

- `id`
- `theme_id`
- `name`
- `slug`
- `page_type`
- `layout_json`
- `default_settings_json`
- `is_active`

### site_templates

Tam bir siteyi reusable başlangıç şablonu olarak saklamak için.

Alanlar:

- `id`
- `name`
- `slug`
- `theme_id`
- `description`
- `snapshot_json`
- `preview_image`
- `is_active`

Bu tablo sayesinde bir siteyi doğrudan "template olarak kaydet" mantığı kurulabilir.

### sites

Gerçek müşteri/proje instance'ları.

Alanlar:

- `id`
- `name`
- `slug`
- `domain`
- `theme_id`
- `site_template_id`
- `tokens_json`
- `custom_css`
- `custom_js`
- `status`

### pages

Mevcut `pages` tablosu korunur, ama aşağıdaki alanlar genişletilebilir:

- `site_id`
- `page_template_id`
- `frontend_variant`
- `sections_json`
- `custom_css`
- `custom_js`

### repeatable content collections

Bazı veri tipleri section içine gömülmek yerine bağımsız yönetilebilir:

- testimonials
- faqs
- team_members
- logo_cloud_items
- service_cards

Bu yapı müşterinin "ekle / sil / sırala" işlerini kolaylaştırır.

## Next.js Frontend Mimarisi

### Sorumluluk

Next.js sadece public rendering yapar.

Görevleri:

- route çözümü
- tema yükleme
- section render
- metadata / SEO
- ISR / cache
- image optimization

### Önerilen Klasör Yapısı

```text
frontend/
  app/
    [...slug]/page.tsx
    blog/[slug]/page.tsx
    layout.tsx
  components/
    sections/
      hero/
      testimonials/
      faq/
      blog-list/
    theme/
      header/
      footer/
      tokens/
  lib/
    api/
    theme/
    sections/
  themes/
    porto-furniture/
    woodmart-fashion/
```

### Section Renderer

Next.js tarafında her section şu mantıkla çalışır:

- `type`
- `variation`
- `props/content`

Renderer:

1. section type'ı okur
2. variation'ı çözer
3. doğru React component'ini çağırır

Örnek:

```ts
renderSection({
  type: 'hero',
  variation: 'porto-split',
  content: { ... }
})
```

Bu sayede Porto ve Woodmart aynı veri modelini paylaşır, yalnız render varyasyonu değişir.

## Laravel API Katmanı

Public frontend için gerekli temel endpoint'ler:

- `GET /api/site`
- `GET /api/settings`
- `GET /api/menus/{location}`
- `GET /api/pages/{slug}`
- `GET /api/articles/{slug}`
- `GET /api/articles`
- `POST /api/forms/{id}/submit`

Response mantığı:

- `theme`
- `tokens`
- `header_variant`
- `footer_variant`
- `sections`
- `seo`
- `breadcrumbs`
- `content`

İlk aşamada öneri:

- mevcut Blade `layout_json` mantığını frontend kaynağı yapma
- Next.js'e sade ve stabil API ver
- section verisini Laravel içinde açık şema ile sakla

## Theme Tokens

Ajansın panelden küçük tasarım değişiklikleri yapabilmesi için token sistemi gerekir.

Örnek token'lar:

- `color_primary`
- `color_secondary`
- `color_accent`
- `heading_scale`
- `body_scale`
- `radius_card`
- `radius_button`
- `shadow_style`
- `container_width`
- `button_style`

Next.js bu değerleri CSS variable olarak uygular.

Bu sayede panelden şu talepler çözülebilir:

- "ana rengi değiştir"
- "başlıkları büyüt"
- "button köşelerini yumuşat"
- "kart gölgelerini hafiflet"

## Custom CSS / JS

Bu ihtiyaç korunmalıdır, ama kontrollü şekilde.

Katmanlar:

- global custom css
- sayfa bazlı custom css
- global custom js
- sayfa bazlı custom js

Ama bu alanlar:

- müşteri için sınırlı
- ajans için tam erişimli

olmalıdır.

## AI ile Template Üretim Akışı

### Girdi

- Porto / Woodmart / başka tema ekran görüntüleri
- referans site URL'leri
- section bazlı ekran parçaları

### AI Çıktısı

- theme preset önerisi
- renk/typography token seti
- section listesi
- section variation isimleri
- Next.js component iskeletleri
- Laravel schema metadata önerisi

### Hedef

AI tek seferlik site üretmesin.

AI şunları üretsin:

- reusable theme
- reusable section
- reusable page template

Bu ürün yaklaşımı, ajansta tekrar kullanılabilirliği yükseltir.

## Porto / Woodmart Gibi Tema Aileleri İçin Doğru Yaklaşım

Bu temalar doğrudan "HTML import" olarak değil, aşağıdaki gibi sistemleştirilmelidir:

- `porto-furniture` = theme pack
- `woodmart-fashion` = theme pack

Her biri için:

- header variants
- footer variants
- hero variants
- listing/detail variants
- CTA/testimonial variants

oluşturulur.

Yani:

- aynı içerik modeli
- farklı görsel aile

mantığı kurulur.

## MVP Önerisi

İlk sürümde hepsi birden yapılmamalı.

### Faz 1

- Laravel public API
- Next.js base frontend
- theme token sistemi
- section renderer
- 5-8 temel section

### Faz 2

- theme packs
- template kayıt sistemi
- site instance sistemi
- blog/archive/detail variations

### Faz 3

- AI-assisted template generation
- "siteyi template olarak kaydet"
- preset klonlama
- preview / versioning

## İlk Eklenmesi Gereken Section Seti

MVP için önerilen section'lar:

- hero
- text-block
- testimonials
- faq
- feature-cards
- cta-banner
- logo-cloud
- gallery
- blog-list
- video
- spacer

Bu liste, kurumsal ve katalog/e-ticaret benzeri sitelerin çoğunu taşır.

## Sonuç

Bu mimari ile:

- mevcut Laravel CMS çöpe gitmez
- frontend modernize edilir
- AI ile template üretimi mümkün hale gelir
- her site reusable asset olur
- aynı template farklı firmalarda tekrar kullanılabilir

Bu yapı, sıradan bir CMS değil; ajans için tekrar üretilebilir bir site motoruna dönüşür.
