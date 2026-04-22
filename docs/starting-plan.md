# Starting Plan

## Başlangıç Kararı

Bu proje şu yaklaşımla başlayacak:

- Laravel backend
- Next.js frontend
- önce `Basic HTML Section Mode`
- sonra `Structured Component Mode`

Bu karar bilinçli olarak seçildi.

## Neden Böyle Başlıyoruz

Çünkü mevcut ajans akışı şu şekilde çalışıyor:

- bir HTML tema seçiliyor
- tema CSS/JS asset'leri ekleniyor
- sayfa HTML'i section'lara ayrılıyor
- içerik, görsel ve sıralama CMS'den yönetiliyor
- küçük farklar custom CSS/JS ile kapatılıyor

Bu akış çalışıyor. Dolayısıyla ilk ürün bunu öldürmemeli; bunu sistematik hale getirmeli.

## Basic HTML Section Mode Nedir

Bu modda frontend yine Next.js olur.

Ama section'lar ilk aşamada tamamen React component olmak zorunda değildir.

Section varlığı şunları taşır:

- theme
- section type
- section html/snippet
- editable fields
- sort order
- active/passive
- page-level overrides

Yani:

- Porto / Woodmart gibi temalar hızlı taşınabilir
- mevcut iş modeli korunur
- ürün bir an önce gerçek projede kullanılabilir hale gelir

## Structured Component Mode Nedir

Bu mod, en çok kullanılan section'ların daha temiz ve tekrar kullanılabilir
React component sürümüdür.

Bu mod ikinci aşamada gelir.

Sebep:

- önce gerçek kullanım verisi görmek
- hangi section'lar gerçekten tekrar kullanılıyor anlamak
- gereksiz abstraction yazmamak

## Nihai Hedef

Uzun vadede sistem hibrit olacak:

- bazı section'lar HTML mode
- bazı section'lar component mode

Zamanla en çok kullanılan bloklar structured moda taşınacak.

## İlk Pratik Hedef

İlk gösterilebilir ürün hedefi:

- `porto-furniture` benzeri bir temayı sisteme almak
- CSS/JS asset'leri bağlamak
- homepage section'larını yönetebilmek
- içerik/görsel/sıralama/custom css ile bir firmaya uyarlayabilmek

## Şimdilik Yapmayacağımız Şey

İlk aşamada şunlara girmiyoruz:

- tam serbest site builder
- her şeyi sıfırdan React component olarak yazmak
- AI ile tam otomatik production site generation

Bunlar daha sonraki fazlara aittir.

## Bu Dosyanın Amacı

Bu dosya, ileride neden bu sırayla başladığımızı hatırlamak için tutulur.

Fazlar ve detaylar revize edilebilir.

Ama şu iki karar referans noktası olarak korunur:

1. frontend Next.js olacak
2. önce Basic HTML Section Mode yapılacak
