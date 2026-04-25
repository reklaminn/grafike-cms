export type ThemeTokens = Record<string, string>;

export type SitePayload = {
  site: {
    name: string;
    domain: string;
    theme: {
      slug: string;
      engine: string;
      assets?: {
        css: string[];
        js: string[];
      };
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

export type MenusPayload = MenuPayload[];

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
  content: Record<string, unknown>;
  custom_css?: string;
  custom_js?: string;
  wrapper_tag?: string | null;
  css_class?: string | null;
  element_id?: string | null;
  inline_style?: string | null;
  custom_attributes?: string | null;
  html_override?: string | null;
};

export type PageRegionBlock = PageSection & {
  region?: "header" | "body" | "footer" | string;
  row_id?: string;
  column_id?: string;
  column_width?: number;
};

export type PageRegionColumn = {
  id: string;
  width: number;
  is_active: boolean;
  responsive?: {
    xs?: number | null;
    sm?: number | null;
    md?: number | null;
    lg?: number | null;
    xl?: number | null;
  };
  css_class?: string | null;
  element_id?: string | null;
  inline_style?: string | null;
  custom_attributes?: string | null;
  blocks: PageRegionBlock[];
};

export type PageRegionRow = {
  id: string;
  type: "row" | string;
  is_active: boolean;
  container?: string | null;
  wrapper_tag?: string | null;
  css_class?: string | null;
  element_id?: string | null;
  inline_style?: string | null;
  custom_attributes?: string | null;
  columns: PageRegionColumn[];
};

export type PageRegions = {
  header: PageRegionRow[];
  body: PageRegionRow[];
  footer: PageRegionRow[];
};

export type PagePayload = {
  page: {
    id: number;
    title: string;
    slug: string;
    template?: string | null;
    sections: PageSection[];
    region_version?: number;
    regions?: PageRegions;
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
