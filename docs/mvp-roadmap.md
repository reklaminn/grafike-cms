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
