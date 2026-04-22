import type { PageSection } from "@/lib/types";

function escapeHtml(value: string | number | boolean | null | undefined): string {
  return String(value ?? "")
    .replaceAll("&", "&amp;")
    .replaceAll("<", "&lt;")
    .replaceAll(">", "&gt;")
    .replaceAll('"', "&quot;")
    .replaceAll("'", "&#039;");
}

function renderTemplateString(template: string, content: PageSection["content"]): string {
  return template.replaceAll(/{{\s*([a-zA-Z0-9_]+)\s*}}/g, (_match, key: string) => {
    return escapeHtml(content[key]);
  });
}

export function renderBasicHtmlSection(section: PageSection): string {
  if (section.html_template) {
    return renderTemplateString(section.html_template, section.content);
  }

  return `
    <section class="section-card" style="padding:24px;">
      <strong>${escapeHtml(section.type)}</strong>
      <p style="margin-top:8px;color:var(--text-soft);">No HTML renderer registered for this section yet.</p>
    </section>
  `;
}
