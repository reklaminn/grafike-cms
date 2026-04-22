# Data Model Draft

Bu doküman Faz 1 öncesi veri modelini netleştirmek için hazırlanmıştır.

Amaç:

- veriyi hangi tablolarla tutacağımızı belirlemek
- hangi şey reusable asset, hangi şey site instance olduğunu ayırmak
- Basic HTML Section Mode ve Structured Component Mode'un aynı model üstünde yaşayabilmesini sağlamak

## Çekirdek Kavramlar

### Theme

Bir tasarım ailesini tanımlar.

Örnek:

- `porto-furniture`
- `woodmart-fashion`

Önerilen tablo: `themes`

Alanlar:

- `id`
- `name`
- `slug`
- `engine`
- `description`
- `assets_json`
- `tokens_json`
- `settings_schema_json`
- `preview_image`
- `is_active`
- `created_at`
- `updated_at`

Not:

- `assets_json` CSS/JS/font/vendor dosyalarını tutar
- `tokens_json` default renk ve tipografi presetlerini tutar

### Section Template

Bir section'ın reusable şablon tanımıdır.

Örnek:

- `hero.porto-split`
- `testimonials.woodmart-carousel`

Önerilen tablo: `section_templates`

Alanlar:

- `id`
- `theme_id`
- `type`
- `variation`
- `name`
- `render_mode`
- `component_key`
- `html_template`
- `schema_json`
- `default_content_json`
- `preview_image`
- `is_active`
- `created_at`
- `updated_at`

Not:

- `render_mode` için ilk iki değer:
  - `html`
  - `component`
- Basic mode'da `html_template` kullanılır
- Structured mode'da `component_key` kullanılır

### Page Template

Sayfa türü bazlı hazır kurgu.

Örnek:

- `porto-furniture-home`
- `woodmart-fashion-blog-index`

Önerilen tablo: `page_templates`

Alanlar:

- `id`
- `theme_id`
- `name`
- `slug`
- `page_type`
- `sections_json`
- `default_settings_json`
- `preview_image`
- `is_active`
- `created_at`
- `updated_at`

### Site Template

Tam bir siteyi reusable başlangıç noktası olarak saklar.

Bu, “siteyi template olarak kaydet” özelliğinin temelidir.

Önerilen tablo: `site_templates`

Alanlar:

- `id`
- `name`
- `slug`
- `theme_id`
- `description`
- `snapshot_json`
- `preview_image`
- `is_active`
- `created_at`
- `updated_at`

### Site Instance

Gerçek müşteri projesidir.

Önerilen tablo: `sites`

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
- `created_at`
- `updated_at`

## Mevcut CMS Modelleri ile İlişki

Mevcut `pages`, `articles`, `menus`, `seo_entries`, `site_settings` tabloları korunur.

Ama çoklu site ve frontend varyasyon desteği için genişletilir.

### pages

Yeni alan önerileri:

- `site_id`
- `page_template_id`
- `frontend_variant`
- `sections_json`
- `custom_css`
- `custom_js`

### articles

Yeni alan önerileri:

- `site_id`
- `listing_variant`
- `detail_variant`

### menus

Yeni alan önerileri:

- `site_id`
- `theme_variant`

## Repeatable Content Collections

Bazı içerikler section içine gömülü tutulabilir.

Ama panelde rahat yönetim için şu repeatable veri tipleri ayrı tablolarla tutulabilir:

- testimonials
- faqs
- team_members
- logo_cloud_items
- service_cards

Bu tipler ileride:

- section içinde referans verilebilir
- farklı sayfalarda tekrar kullanılabilir

## Basic HTML Section Mode Veri Yapısı

Page üstünde saklanan section instance örneği:

```json
[
  {
    "id": "sec_hero_1",
    "type": "hero",
    "variation": "porto-split",
    "render_mode": "html",
    "section_template_id": 12,
    "is_active": true,
    "sort_order": 1,
    "content": {
      "title": "Modern Furniture Collections",
      "subtitle": "Reusable homepage hero content",
      "button_text": "Discover Collection",
      "button_url": "/collections"
    },
    "custom_css": "",
    "custom_js": ""
  }
]
```

## Structured Component Mode Veri Yapısı

Structured mode da aynı gövdeyi kullanır, sadece render değişir:

```json
[
  {
    "id": "sec_testimonials_1",
    "type": "testimonials",
    "variation": "grid",
    "render_mode": "component",
    "component_key": "testimonials.grid",
    "is_active": true,
    "sort_order": 3,
    "content": {
      "title": "Customer Feedback",
      "items_source": "testimonials_collection",
      "items_limit": 6
    }
  }
]
```

## Temel Karar

Veri modeli baştan hibrit olmalı.

Yani:

- Basic HTML Section Mode geçici hack olmamalı
- Structured Component Mode için ayrı ikinci veri modeli kurulmamlı

Tek model, iki render modu yaklaşımı kullanılmalı.
