import { createElement } from "react";
import { HtmlSection } from "@/components/sections/html-section";
import { ArticleListSection } from "@/components/sections/article-list-section";
import { FormSectionLoader } from "@/components/sections/form-section-loader";
import type { MenusPayload, PageSection, SettingsPayload, SitePayload } from "@/lib/types";
import { buildElementProps } from "@/lib/sections/element-props";
import { renderBasicHtmlSection } from "@/lib/sections/basic-html-renderer";

type SectionRendererProps = {
  section: PageSection;
  site: SitePayload["site"];
  settings: SettingsPayload["settings"];
  menus: MenusPayload;
  /** Current page ID — forwarded to article-list sections for dynamic fetching */
  pageId?: number;
  /** Current language code — forwarded to article-list sections */
  lang?: string;
};

export function SectionRenderer({ section, site, settings, menus, pageId, lang }: SectionRendererProps) {
  // ── Dynamic data sections ─────────────────────────────────────────────
  if (section.type === "article-list") {
    return <ArticleListSection section={section} pageId={pageId} lang={lang} />;
  }

  if (section.type === "form") {
    return <FormSectionLoader section={section} />;
  }

  // ── HTML template engine ──────────────────────────────────────────────
  const props = buildElementProps({
    className: section.css_class,
    id: section.element_id,
    inlineStyle: section.inline_style,
    customAttributes: section.custom_attributes,
  });

  if (section.render_mode === "html") {
    const html = renderBasicHtmlSection(section, { site, settings, menus });

    if (!section.wrapper_tag) {
      return <HtmlSection html={html} />;
    }

    const tag = section.wrapper_tag;

    return createElement(tag, props, <HtmlSection html={html} />);
  }

  // ── Component placeholder ─────────────────────────────────────────────
  const tag = section.wrapper_tag || "section";

  return createElement(
    tag,
    props,
    <section className="section-card" style={{ padding: 24 }}>
      <strong>Structured component placeholder:</strong> {section.type}.{section.variation}
    </section>,
  );
}
