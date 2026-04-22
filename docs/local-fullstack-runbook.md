# Local Full-Stack Runbook

Bu doküman Faz 1 sonunda mevcut iskeleti yerelde test etmek için minimum akışı tarif eder.

Bu aşamada sistem şunları gerçekten yapabiliyor:

- Laravel public API ayağa kalkar
- demo `theme + site + section_template + page` verisi seed edilir
- Next.js frontend bu veriyi Laravel API'den çeker
- `home`, `settings`, `header menu` ve section render akışı gerçek API üstünden çalışır

## 1. Laravel backend

Repo kökünde:

```bash
cd /Volumes/Dev/iraspa-cms
cp .env.example .env
touch database/database.sqlite
composer install
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```

Beklenen:

- Laravel `http://127.0.0.1:8000` adresinde çalışır
- seed sonunda `Frontend template demo verisi oluşturuldu` mesajı görünür

## 2. Next.js frontend

İkinci terminalde:

```bash
cd /Volumes/Dev/iraspa-cms/apps/frontend
cp .env.example .env.local
npm install
npm run dev
```

Beklenen:

- Next.js `http://127.0.0.1:3000` adresinde çalışır
- frontend Laravel API'den veri çekmeye başlar

## 3. İlk kontrol

Tarayıcıda aç:

- `http://127.0.0.1:3000`
- `http://127.0.0.1:3000/home`
- `http://127.0.0.1:3000/blog`

Beklenen:

- header içinde site adı ve menü görünür
- `home` sayfasında backend'den gelen `hero` ve `features` section'ları görünür
- footer içinde seed edilen iletişim bilgisi görünür

## 4. API kontrolü

Backend response'ları ayrıca test etmek için:

```bash
curl http://127.0.0.1:8000/api/v1/site
curl http://127.0.0.1:8000/api/v1/settings
curl http://127.0.0.1:8000/api/v1/menus/header
curl http://127.0.0.1:8000/api/v1/pages/home
```

## 5. Test komutları

Repo kökünde:

```bash
php artisan test --filter=PublicSiteApiTest
php artisan test --filter=FrontendPageTest
```

Frontend build:

```bash
cd /Volumes/Dev/iraspa-cms/apps/frontend
npm run build
```

## Notlar

- `Site::resolve()` şu an site host header gelmezse `is_primary=true` olan aktif siteye düşer.
- Bu yüzden localde `demo.grafike.test` domain tanımlamadan da akış çalışır.
- Next.js build sırasında çoklu `package-lock.json` uyarısı var; build'i kırmıyor, sonra temizlenebilir.

## Bu Fazda Bilinen Sınırlar

- admin panelde henüz `theme/site/section template` yönetim ekranı yok
- section HTML'leri placeholder replacement ile render ediliyor
- structured component mode henüz başlamadı
- live preview / visual editor henüz yok
