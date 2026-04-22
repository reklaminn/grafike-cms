import type { PagePayload, SitePayload } from "@/lib/types";

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
    footer_variant: "porto-furniture-footer"
  }
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
          is_active: true,
          content: {
            title: "Modern Furniture Collections",
            subtitle: "Basic HTML Section Mode ile ilk Porto benzeri giriş sayfası.",
            button_text: "Discover Collection",
            button_url: "/collections"
          }
        },
        {
          id: "features_1",
          type: "feature-cards",
          variation: "porto-icons-3up",
          render_mode: "html",
          is_active: true,
          content: {
            title: "Why customers choose this brand",
            item_1_title: "Premium Materials",
            item_1_body: "Solid materials and warm finishes.",
            item_2_title: "Fast Delivery",
            item_2_body: "Reliable stock and dispatch planning.",
            item_3_title: "Design Support",
            item_3_body: "Project-based consultation for architects."
          }
        },
        {
          id: "cta_1",
          type: "cta-banner",
          variation: "porto-dark",
          render_mode: "html",
          is_active: true,
          content: {
            title: "Need a custom furniture quote?",
            body: "This block is rendered from a raw HTML section template in Faz 1.",
            button_text: "Request Offer",
            button_url: "/contact"
          }
        }
      ]
    },
    seo: {
      title: "Home",
      description: "Mock homepage payload",
      canonical: "http://localhost:3000/home"
    }
  }
};

export function mockPagePayload(slug: string): PagePayload | null {
  return pages[slug] ?? null;
}
