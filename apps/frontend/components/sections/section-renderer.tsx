import { createElement } from "react";
import { HtmlSection } from "@/components/sections/html-section";
import type { MenusPayload, PageSection, SettingsPayload, SitePayload } from "@/lib/types";
import { buildElementProps } from "@/lib/sections/element-props";
import { renderBasicHtmlSection } from "@/lib/sections/basic-html-renderer";

type SectionRendererProps = {
  section: PageSection;
  site: SitePayload["site"];
  settings: SettingsPayload["settings"];
  menus: MenusPayload;
};

export function SectionRenderer({ section, site, settings, menus }: SectionRendererProps) {
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

  const tag = section.wrapper_tag || "section";

  return createElement(
    tag,
    props,
    <section className="section-card" style={{ padding: 24 }}>
      <strong>Structured component placeholder:</strong> {section.type}.{section.variation}
    </section>,
  );
}
