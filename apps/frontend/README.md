# Frontend App

Bu klasör hedefte Next.js tabanlı public frontend uygulamasını barındıracak.

Sorumluluklar:

- slug bazlı route çözümü
- theme pack yükleme
- section render
- metadata / SEO
- cache / ISR
- görsel optimizasyonu

Render mantığı:

- Laravel API'den sayfa verisi alınır
- `theme + tokens + sections` çözülür
- section renderer doğru React componentlerini çağırır

Örnek:

```ts
renderSection({
  type: "hero",
  variation: "porto-split",
  content: { ... }
})
```
