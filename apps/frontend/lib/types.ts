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

export type ArticleCover = {
  url: string;
  thumb: string;
  alt: string;
} | null;

export type ArticleListItem = {
  id: number;
  title: string;
  slug: string;
  excerpt: string | null;
  display_date: string | null;
  published_at: string | null;
  is_featured: boolean;
  cover: ArticleCover;
  author: { id: number; name: string } | null;
  language: { id: number; code: string } | null;
  page: { id: number; title: string; slug: string } | null;
};

export type ArticleListPayload = {
  data: ArticleListItem[];
  meta: {
    current_page: number;
    per_page: number;
    total: number;
    last_page: number;
  };
};

export type FormField = {
  id: number;
  name: string;
  label: string;
  type: "text" | "email" | "textarea" | "select" | "checkbox" | "radio" | "tel" | "url" | "number" | "date" | "file" | "hidden";
  placeholder?: string | null;
  default_value?: string | null;
  options: Array<{ label: string; value: string }> | string[];
  is_required: boolean;
  css_class?: string | null;
  section?: string | null;
};

export type FormPayload = {
  id: number;
  name: string;
  slug: string;
  description?: string | null;
  requires_captcha: boolean;
  fields: FormField[];
};

export type ArticleBlock = {
  type: "heading" | "paragraph" | "image" | "video" | "html";
  level?: number;
  text?: string;
  content?: string;
  images?: Array<{ url: string; alt?: string; caption?: string }>;
  url?: string;
  alt?: string;
  caption?: string;
  embed_url?: string;
  code?: string;
};

export type ArticleDetail = {
  id: number;
  title: string;
  slug: string;
  excerpt: string | null;
  body: string | null;
  content_json: ArticleBlock[];
  display_date: string | null;
  published_at: string | null;
  listing_variant: string | null;
  detail_variant: string | null;
  is_featured: boolean;
  cover: ArticleCover;
  gallery: Array<{ url: string; thumb: string; name: string; alt: string }>;
};

export type ArticleDetailPayload = {
  article: ArticleDetail;
  author: { id: number; name: string } | null;
  language: { id: number; code: string; locale: string; name: string } | null;
  page: { id: number; title: string; slug: string } | null;
  seo: { title: string; description: string; canonical: string; noindex: boolean };
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
