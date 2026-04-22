export type ThemeTokens = Record<string, string>;

export type SitePayload = {
  site: {
    name: string;
    domain: string;
    theme: {
      slug: string;
      engine: string;
    };
    tokens: ThemeTokens;
    header_variant: string;
    footer_variant: string;
    locale?: string;
    available_locales?: Array<{
      code: string;
      locale: string;
      name: string;
    }>;
  };
};

export type MenuItem = {
  id: number;
  title: string;
  url: string;
  target: string | null;
  children: MenuItem[];
};

export type MenuPayload = {
  id: number;
  name: string;
  slug: string;
  location: string;
  items: MenuItem[];
};

export type SettingsPayload = {
  settings: {
    site_title: string;
    logo_url: string;
    favicon_url: string;
    footer_text: string;
    contact: {
      phone: string;
      email: string;
      address: string;
    };
    social: Record<string, string>;
    services: Record<string, string>;
  };
};

export type PageSection = {
  id: string;
  type: string;
  variation: string;
  render_mode: "html" | "component";
  section_template_id?: number;
  template_name?: string;
  html_template?: string | null;
  component_key?: string | null;
  schema?: Record<string, unknown>;
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
    template?: string | null;
    sections: PageSection[];
  };
  seo: {
    title: string;
    description: string;
    canonical: string;
  };
  theme?: {
    slug: string;
  };
};
