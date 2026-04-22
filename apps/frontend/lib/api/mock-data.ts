import type { MenuPayload, PagePayload, SettingsPayload, SitePayload } from "@/lib/types";

export const mockSitePayload: SitePayload = {
  site: {
    name: "Grafike Furniture",
    domain: "grafike.local",
    theme: {
      slug: "porto-furniture",
      engine: "next"
    },
    tokens: {
      color_primary: "#7c5a3a",
      color_secondary: "#f3ede6",
      color_accent: "#111827",
      radius_card: "20px",
      radius_button: "999px",
      container_width: "1320px"
    },
    header_variant: "porto-furniture-header",
    footer_variant: "porto-furniture-footer",
    locale: "tr"
  }
};

export const mockSettingsPayload: SettingsPayload = {
  settings: {
    site_title: "Grafike Furniture",
    logo_url: "",
    favicon_url: "",
    footer_text: "© 2026 Grafike Furniture",
    contact: {
      phone: "+90 212 555 0000",
      email: "hello@grafike.test",
      address: "Istanbul, Turkiye"
    },
    social: {
      instagram: "https://instagram.com/grafike"
    },
    services: {}
  }
};

export const mockHeaderMenuPayload: MenuPayload = {
  id: 1,
  name: "Header Menu",
  slug: "header-tr",
  location: "header",
  items: [
    {
      id: 1,
      title: "Ana Sayfa",
      url: "/home",
      target: "_self",
      children: []
    },
    {
      id: 2,
      title: "Blog",
      url: "/blog",
      target: "_self",
      children: []
    }
  ]
};

const pages: Record<string, PagePayload> = {
  home: {
    page: {
      id: 1,
      title: "Home",
      slug: "home",
      sections: [
        {
          id: "hero_1",
          type: "hero",
          variation: "porto-split",
          render_mode: "html",
          section_template_id: 1,
          template_name: "Hero / Porto Split",
          html_template:
            '<section class="hero hero--porto-split"><div class="container"><p class="eyebrow">{{eyebrow}}</p><h1>{{title}}</h1><p class="subtitle">{{subtitle}}</p><a class="button" href="{{button_url}}">{{button_text}}</a></div></section>',
          is_active: true,
          content: {
            eyebrow: "Grafike Demo",
            title: "Modern Furniture Collections",
            subtitle: "Basic HTML Section Mode ile ilk Porto benzeri giriş sayfası.",
            button_text: "Discover Collection",
            button_url: "/collections"
          }
        },
        {
          id: "features_1",
          type: "features",
          variation: "porto-icons",
          render_mode: "html",
          section_template_id: 2,
          template_name: "Features / Porto Icons",
          html_template:
            '<section class="features features--porto-icons"><div class="container"><h2>{{title}}</h2><p>{{description}}</p></div></section>',
          is_active: true,
          content: {
            title: "Why customers choose this brand",
            description: "This homepage is now coming from the Laravel public API instead of static mock-only wiring."
          }
        }
      ]
    },
    seo: {
      title: "Home",
      description: "Mock homepage payload",
      canonical: "http://localhost:3000/home"
    },
    theme: {
      slug: "porto-furniture"
    }
  }
};

export function mockPagePayload(slug: string): PagePayload | null {
  return pages[slug] ?? null;
}
