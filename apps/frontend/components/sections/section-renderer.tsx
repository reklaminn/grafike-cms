import { createElement } from "react";
import { HtmlSection } from "@/components/sections/html-section";
import type { PageSection } from "@/lib/types";
import { buildElementProps } from "@/lib/sections/element-props";
import { renderBasicHtmlSection } from "@/lib/sections/basic-html-renderer";

type SectionRendererProps = {
  section: PageSection;
};

export function SectionRenderer({ section }: SectionRendererProps) {
  const tag = section.wrapper_tag || "section";
  const props = buildElementProps({
    className: section.css_class,
    id: section.element_id,
    inlineStyle: section.inline_style,
    customAttributes: section.custom_attributes,
  });

  if (section.render_mode === "html") {
    const html = renderBasicHtmlSection(section);
    return createElement(tag, props, <HtmlSection html={html} />);
  }

  return createElement(
    tag,
    props,
    <section className="section-card" style={{ padding: 24 }}>
      <strong>Structured component placeholder:</strong> {section.type}.{section.variation}
    </section>,
  );
}
