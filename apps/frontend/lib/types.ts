export type ThemeTokens = Record<string, string>;

export type SitePayload = {
  site: {
    name: string;
    domain: string;
    theme: {
      slug: string;
      engine: "next";
    };
    tokens: ThemeTokens;
    header_variant: string;
    footer_variant: string;
  };
};

export type PageSection = {
  id: string;
  type: string;
  variation: string;
  render_mode: "html" | "component";
  is_active: boolean;
  content: Record<string, string | number | boolean | null>;
  custom_css?: string;
  custom_js?: string;
};

export type PagePayload = {
  page: {
    id: number;
    title: string;
    slug: string;
    sections: PageSection[];
  };
  seo: {
    title: string;
    description: string;
    canonical: string;
  };
};
