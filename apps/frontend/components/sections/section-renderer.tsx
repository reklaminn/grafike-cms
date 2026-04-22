import { HtmlSection } from "@/components/sections/html-section";
import type { PageSection } from "@/lib/types";
import { renderBasicHtmlSection } from "@/lib/sections/basic-html-renderer";

type SectionRendererProps = {
  section: PageSection;
};

export function SectionRenderer({ section }: SectionRendererProps) {
  if (section.render_mode === "html") {
    const html = renderBasicHtmlSection(section);
    return <HtmlSection html={html} />;
  }

  return (
    <section className="section-card" style={{ padding: 24 }}>
      <strong>Structured component placeholder:</strong> {section.type}.{section.variation}
    </section>
  );
}
