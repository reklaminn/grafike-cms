# Public API Contract Draft

Bu doküman, Faz 1'de Laravel tarafından üretilecek ilk public API sözleşmesini tanımlar.

Hedef:

- Next.js frontend'in ihtiyaç duyduğu minimum veriyi netleştirmek
- ilk sürümde gereksiz API karmaşıklığından kaçınmak
- stable bir response yapısı tanımlamak

## Genel İlkeler

- public API sadece published içerik döner
- response'lar site bazlı çalışır
- frontend için gerekli metadata response içine gömülü gelir
- mümkün olduğunda tek endpoint, yeterli bağlamı birlikte döner

## 1. Site Endpoint

### `GET /api/site`

Amaç:

- aktif site instance bilgisi
- aktif theme
- global token'lar
- header/footer varyasyonları

Örnek response:

```json
{
  "site": {
    "name": "Grafike Furniture",
    "domain": "example.com",
    "theme": {
      "slug": "porto-furniture",
      "engine": "next"
    },
    "tokens": {
      "color_primary": "#7c5a3a",
      "color_secondary": "#f3ede6",
      "radius_card": "20px"
    },
    "header_variant": "porto-furniture-header",
    "footer_variant": "porto-furniture-footer"
  }
}
```

## 2. Settings Endpoint

### `GET /api/settings`

Amaç:

- iletişim bilgileri
- sosyal linkler
- logo ve favicon
- analytics/recaptcha gibi public ayarlar

Örnek response:

```json
{
  "settings": {
    "site_title": "Grafike Furniture",
    "logo_url": "/media/logo.png",
    "favicon_url": "/media/favicon.png",
    "contact": {
      "phone": "+90 555 000 00 00",
      "email": "hello@example.com"
    },
    "social": {
      "instagram": "https://instagram.com/example"
    }
  }
}
```

## 3. Menus Endpoint

### `GET /api/menus/{location}`

Amaç:

- `header`, `footer`, `mobile`, `corporate` gibi menu lokasyonlarını çekmek

Örnek response:

```json
{
  "location": "header",
  "items": [
    {
      "id": 1,
      "title": "Home",
      "url": "/",
      "target": null,
      "children": []
    }
  ]
}
```

## 4. Page Detail Endpoint

### `GET /api/pages/{slug}`

Amaç:

- public sayfa verisini ve tüm frontend bağlamını döndürmek

Örnek response:

```json
{
  "page": {
    "id": 10,
    "title": "Home",
    "slug": "home",
    "excerpt": null,
    "featured_image": "/media/home-cover.jpg",
    "template": "porto-furniture-home",
    "sections": [
      {
        "id": "sec_hero_1",
        "type": "hero",
        "variation": "porto-split",
        "render_mode": "html",
        "is_active": true,
        "content": {
          "title": "Modern Furniture Collections"
        }
      }
    ]
  },
  "seo": {
    "title": "Home",
    "description": "Homepage description",
    "canonical": "https://example.com/"
  },
  "breadcrumbs": [],
  "theme": {
    "slug": "porto-furniture"
  }
}
```

## 5. Article Detail Endpoint

### `GET /api/articles/{slug}`

Amaç:

- blog/article detail page için veri vermek

Örnek response:

```json
{
  "article": {
    "id": 25,
    "title": "New Collection",
    "slug": "new-collection",
    "excerpt": "Short summary",
    "body": "<p>Content</p>",
    "published_at": "2026-04-22T10:00:00Z",
    "featured_image": "/media/article-cover.jpg",
    "gallery": []
  },
  "page": {
    "id": 10,
    "title": "Blog",
    "slug": "blog"
  },
  "seo": {
    "title": "New Collection",
    "description": "Short summary"
  }
}
```

## 6. Article Listing Endpoint

### `GET /api/articles`

Amaç:

- archive/blog list page verisi

Query örnekleri:

- `?page=1`
- `?category=chairs`
- `?limit=12`

Örnek response:

```json
{
  "items": [
    {
      "id": 25,
      "title": "New Collection",
      "slug": "new-collection",
      "excerpt": "Short summary",
      "featured_image": "/media/article-cover.jpg",
      "published_at": "2026-04-22T10:00:00Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 12,
    "total": 42,
    "last_page": 4
  }
}
```

## 7. Form Submit Endpoint

### `POST /api/forms/{id}/submit`

Amaç:

- public frontend form submit işlemleri

İlk fazda sadece mevcut CMS form altyapısını açmak yeterlidir.

## İlk Fazda Bilinçli Olarak Yapılmayacaklar

- GraphQL
- preview mode
- full theme editor API
- template create/update API
- AI-assisted generation API

Bunlar ileriki fazlara bırakılmalıdır.

## Faz 1 İçin Minimum Başarı

Faz 1 public API başarılı sayılır, eğer:

1. tek bir site theme + sections + menus + settings ile Next.js'te render olabiliyorsa
2. blog detail ve blog list sayfası beslenebiliyorsa
3. published filtreleri net çalışıyorsa
