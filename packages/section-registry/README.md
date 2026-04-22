# Section Registry

Section registry, frontend'de render edilebilen reusable blokların metadata
katmanıdır.

Her section:

- bir `type`
- bir `variation`
- bir `schema`
- bir `component_key`

taşır.

Örnek:

```json
{
  "type": "testimonials",
  "variation": "carousel",
  "component_key": "testimonials.carousel",
  "schema": {
    "fields": [
      { "name": "title", "type": "text" },
      { "name": "items", "type": "repeater" }
    ]
  }
}
```
