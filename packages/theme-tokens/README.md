# Theme Tokens

Theme token sistemi, panelden küçük görsel ayar değişiklikleri yapabilmek için
tasarlanır.

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

Frontend bu token'ları CSS variable olarak uygular.

Örnek:

```css
:root {
  --color-primary: #0f766e;
  --radius-card: 24px;
  --font-size-h1: 56px;
}
```
