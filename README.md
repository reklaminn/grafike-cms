# Grafike Next.js + Laravel CMS

Ajans kullanımına uygun, tekrar kullanılabilir tema/template sistemi hedefleyen
bir site motoru.

Bu repo şu problemi çözmek için hazırlanıyor:

- Aynı CMS altyapısıyla farklı görsel ailelerde siteler üretebilmek
- Porto / Woodmart / özel referans tasarımları reusable template'e çevirmek
- Her siteyi daha sonra yeniden kullanılabilir template olarak kaydedebilmek
- Laravel'i admin/CMS/backoffice olarak, Next.js'i public frontend olarak kullanmak
- AI araçlarını tema/section/template üretiminde hızlandırıcı olarak kullanmak

## Hedef Model

- `apps/cms`: Laravel tabanlı admin panel, içerik yönetimi, SEO, medya, template metadata
- `apps/frontend`: Next.js tabanlı public frontend renderer
- `packages/section-registry`: section tipleri, varyasyonlar ve schema tanımları
- `packages/theme-tokens`: global tema token yapısı
- `examples/theme-packs`: Porto / Woodmart benzeri örnek tema presetleri
- `examples/site-templates`: tekrar kullanılabilir site/template snapshot örnekleri

## Ana Ürün Fikri

Sistem 4 ana kavram üstüne kurulacak:

1. Theme Pack
2. Section Library
3. Template
4. Site Instance

Örnek kullanım:

- Firma A -> `porto-furniture`
- Firma B -> `woodmart-fashion`

İkisi de aynı Laravel backend'i kullanır, ama farklı theme pack ve section
varyasyonları ile render edilir.

## Bu Repoda Şu An Ne Var

- mimari dokümanı
- monorepo iskelet önerisi
- örnek theme pack dosyası
- örnek site template dosyası

Detaylı mimari için:

- [docs/nextjs-laravel-template-engine-architecture.md](docs/nextjs-laravel-template-engine-architecture.md)

## Önerilen Klasör Yapısı

```text
apps/
  cms/
  frontend/
packages/
  section-registry/
  theme-tokens/
examples/
  theme-packs/
  site-templates/
docs/
```

## Planlanan Fazlar

### Faz 1

- Laravel public API katmanı
- Next.js temel app iskeleti
- theme token sistemi
- section renderer
- temel section seti

### Faz 2

- theme packs
- page templates
- site instance yapısı
- blog/archive/detail varyasyonları

### Faz 3

- AI-assisted template generation
- siteyi template olarak kaydetme
- preset klonlama
- preview / versioning

## İlk Template Aileleri

Başlangıç için önerilen tema aileleri:

- `porto-furniture`
- `porto-corporate`
- `woodmart-fashion`

## Not

Bu repo şu aşamada üretim kodundan çok ürün mimarisi ve başlangıç yapısını
toplamak için kullanılıyor. Kod geçişi fazlı ilerlemeli; mevcut Laravel CMS
aniden taşınmamalı.
