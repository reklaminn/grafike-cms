# Sentry Observability Plan

Bu doküman, yeni `Laravel CMS + Next.js frontend` mimarisinde hata takibini
nasıl kuracağımızı tanımlar.

## Kısa Karar

Sentry bu sistem için uygundur ve önerilir.

Sebep:

- hata kaynağı ikiye ayrılıyor: backend ve frontend
- tema/site bazlı sorunları etiketlemek gerekiyor
- deploy sonrası regressions hızlı fark edilmek isteniyor
- ajans ölçeğinde birden fazla site instance izlenebilir olmalı

## İzleme Katmanları

### 1. Laravel Backend

Laravel tarafında Sentry şu olayları toplamalıdır:

- API exception'ları
- 500 seviyesindeki backend hataları
- queue job hataları
- mail / form submit / review submit hataları
- template / site / settings çözümleme hataları

Özel önem taşıyan alanlar:

- API response generation errors
- SEO resolve / redirect resolve sorunları
- media URL üretim hataları
- site settings / theme settings parse hataları

### 2. Next.js Frontend

Next.js tarafında Sentry şu olayları toplamalıdır:

- SSR / route render hataları
- fetch / hydration hataları
- section renderer hataları
- HTML section parsing/render hataları
- client-side runtime hataları

Özel önem taşıyan alanlar:

- `Basic HTML Section Mode` render hataları
- future `Structured Component Mode` section failures
- theme/token uygulanırken çıkan görsel kırılmaların izlenebilir hata karşılıkları

## Ortak Tag Yapısı

Hem backend hem frontend tarafında ortak tag standardı kullanılmalıdır:

- `app=backend` veya `app=frontend`
- `environment=local|staging|production`
- `site_slug`
- `site_domain`
- `theme_slug`
- `template_slug`
- `render_mode=html|component`

Bu sayede şu filtreler mümkün olur:

- sadece belirli müşterinin hataları
- sadece belirli tema ailesinin hataları
- sadece `html section mode` hataları
- sadece production frontend hataları

## Önerilen Context Alanları

Sentry event'lerine şu context alanları eklenmelidir:

### Backend Context

- authenticated admin id
- authenticated member id
- request path
- API version
- locale
- page id
- article id

### Frontend Context

- current slug
- route kind (`page`, `article`, `archive`)
- theme slug
- tokens snapshot hash
- section id
- section type
- section variation

## Release ve Deploy Takibi

Sentry yalnız hata toplamak için değil, release takibi için de kullanılmalıdır.

Öneri:

- Laravel backend için ayrı release name
- Next.js frontend için ayrı release name
- ikisinde de git SHA tutulmalı

Örnek:

- `grafike-cms-backend@<sha>`
- `grafike-frontend@<sha>`

Bu sayede:

- hangi deploy'dan sonra hata başladı
- aynı release farklı sitelerde kırılıyor mu

takip edilebilir.

## Environment Stratejisi

En az 3 environment:

- `local`
- `staging`
- `production`

Kural:

- local düşük örnekleme ile
- staging tam örnekleme ile
- production kontrollü örnekleme ile

Özellikle frontend tarafında performans ve hata örnekleme dikkatli ayarlanmalıdır.

## Basic HTML Section Mode İçin Özel Plan

Bu modda section'lar reusable React component değil, HTML snippet/render mantığı ile çalışacağı için şu riskler vardır:

- bozuk placeholder mapping
- eksik HTML field
- beklenmeyen custom CSS etkileri
- tema asset çakışmaları

Bu yüzden `html` mode için ek tag'ler önerilir:

- `section_template_id`
- `html_section_type`
- `html_section_variation`

Ve mümkünse yakalanan exception message'lerinde:

- hangi section render edilirken hata oluştuğu
- hangi site/template altında olduğu

loglanmalıdır.

## Structured Component Mode İçin Özel Plan

Bu modda daha deterministik bir yapı olacağı için:

- component key
- section schema version
- variation key

mutlaka tag veya context olarak eklenmelidir.

Örnek:

- `component_key=hero.porto-split`
- `schema_version=1`

## Sentry Kurulum Sırası

Teknik sıralama şu olmalı:

### Adım 1

Laravel backend Sentry kurulumu

Çünkü:

- API ve içerik yönetimi orada
- ilk üretim hata yüzeyi backend'de daha kritik

### Adım 2

Next.js frontend Sentry kurulumu

Çünkü:

- Faz 1'de frontend gerçekten devreye girdiğinde section render ve fetch katmanı önem kazanacak

### Adım 3

Release tagging + deploy correlation

### Adım 4

Site/theme/template bazlı enrich edilmiş tags

## İlk Uygulama İçin Minimum Gereksinim

Minimum başarılı Sentry entegrasyonu için şunlar yeterlidir:

- backend exception capture
- frontend runtime exception capture
- environment tagging
- `app=backend/frontend` tagging

## Fazlara Etkisi

### Faz 1

- backend Sentry kur
- frontend Sentry için planı hazır tut

### Faz 2

- frontend Sentry kur
- section/component tags ekle

### Faz 3 ve sonrası

- site/template/theme bazlı observability derinleştir
- müşteri bazlı dashboard ve filtre standartları oluştur
