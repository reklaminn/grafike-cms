# Phase 0 Checklist

Bu dosya, Faz 0 tamamlandı mı sorusuna net cevap vermek için tutulur.

## Faz 0 Amacı

Faz 0'ın amacı kod üretmek değil, yanlış kod üretimini önlemektir.

Bu faz sonunda şu soruların cevabı net olmalıdır:

- sistemin ana yönü nedir?
- frontend ve backend sorumlulukları nasıl ayrılır?
- veri modeli hangi kavramlar üstüne kurulur?
- ilk public API hangi alanları dönecektir?
- Basic HTML Section Mode tam olarak ne demektir?
- Faz 1'e hangi teknik sözleşmelerle geçilecektir?

## Tamamlanması Gerekenler

### 1. Ürün Yönü

- [x] Laravel backend / Next.js frontend kararı
- [x] önce Basic HTML Section Mode, sonra Structured Component Mode kararı
- [x] theme pack + template + site instance yaklaşımı

### 2. Temel Dokümantasyon

- [x] ana mimari dokümanı
- [x] başlangıç planı
- [x] roadmap
- [ ] Faz 0 karar günlüğü / ADR seti

### 3. Veri Modeli

- [ ] theme
- [ ] section template
- [ ] page template
- [ ] site template
- [ ] site instance
- [ ] page sections
- [ ] global and page-level overrides

### 4. Public API Sözleşmesi

- [ ] site endpoint
- [ ] settings endpoint
- [ ] menus endpoint
- [ ] page detail endpoint
- [ ] article detail endpoint
- [ ] article listing endpoint

### 5. Repo Yapısı

- [x] `apps/`
- [x] `packages/`
- [x] `examples/`
- [ ] `apps/frontend` gerçek Next.js app skeleton
- [ ] `apps/cms` Laravel API migration strategy

### 6. Tema Geçiş Stratejisi

- [ ] mevcut HTML tema -> theme pack dönüşüm akışı
- [ ] HTML section snippet formatı
- [ ] editable field mapping formatı
- [ ] custom css/js override kuralları

## Faz 0 Çıkış Kriteri

Faz 0 ancak şu durumda tamam sayılır:

1. Faz 1'e başlayacak bir geliştirici ne inşa edeceğini dokümanlardan anlayabiliyorsa
2. veri modeli belirsiz değilse
3. ilk API response yapıları netse
4. basic mode ile structured mode ayrımı yazılıysa

## Faz 0 Sonrası İlk Teknik İşler

Faz 1'e geçerken ilk kod işleri:

1. Next.js app skeleton
2. Laravel public API route listesi
3. örnek `porto-furniture` theme pack loader
4. HTML section record formatı
5. tek bir homepage'i basic mode ile render etmek
