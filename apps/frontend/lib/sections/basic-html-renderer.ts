import type { PageSection } from "@/lib/types";

function escapeHtml(value: string | number | boolean | null | undefined): string {
  return String(value ?? "")
    .replaceAll("&", "&amp;")
    .replaceAll("<", "&lt;")
    .replaceAll(">", "&gt;")
    .replaceAll('"', "&quot;")
    .replaceAll("'", "&#039;");
}

function heroTemplate(section: PageSection): string {
  const eyebrow = escapeHtml(section.content.eyebrow);
  const title = escapeHtml(section.content.title);
  const subtitle = escapeHtml(section.content.subtitle);
  const buttonText = escapeHtml(section.content.button_text);
  const buttonUrl = escapeHtml(section.content.button_url);

  return `
    <section class="section-card" style="padding:40px;background:linear-gradient(135deg,var(--color-secondary),#fff);">
      <div style="display:grid;gap:18px;">
        <span style="font-size:12px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--color-primary);">${eyebrow || "Porto Inspired Hero"}</span>
        <h1 style="margin:0;font-size:clamp(40px,8vw,72px);line-height:1;font-weight:900;">${title}</h1>
        <p style="margin:0;max-width:720px;color:var(--text-soft);font-size:18px;line-height:1.7;">${subtitle}</p>
        <div><a class="btn-primary" href="${buttonUrl}">${buttonText}</a></div>
      </div>
    </section>
  `;
}

function featuresTemplate(section: PageSection): string {
  const title = escapeHtml(section.content.title);
  const description = escapeHtml(section.content.description);

  return `
    <section class="section-card" style="padding:32px;">
      <div style="display:grid;gap:24px;">
        <h2 style="margin:0;font-size:32px;font-weight:800;">${title}</h2>
        <p style="margin:0;max-width:840px;color:var(--text-soft);line-height:1.8;">${description}</p>
      </div>
    </section>
  `;
}

function ctaBannerTemplate(section: PageSection): string {
  const title = escapeHtml(section.content.title);
  const body = escapeHtml(section.content.body);
  const buttonText = escapeHtml(section.content.button_text);
  const buttonUrl = escapeHtml(section.content.button_url);

  return `
    <section class="section-card" style="padding:32px;background:#111827;color:#fff;">
      <div style="display:flex;flex-wrap:wrap;gap:24px;align-items:center;justify-content:space-between;">
        <div style="display:grid;gap:10px;max-width:760px;">
          <h2 style="margin:0;font-size:32px;font-weight:900;">${title}</h2>
          <p style="margin:0;color:rgba(255,255,255,.8);line-height:1.7;">${body}</p>
        </div>
        <div>
          <a class="btn-primary" style="background:#fff;color:#111827;" href="${buttonUrl}">${buttonText}</a>
        </div>
      </div>
    </section>
  `;
}

export function renderBasicHtmlSection(section: PageSection): string {
  if (section.type === "hero") {
    return heroTemplate(section);
  }

  if (section.type === "features" || section.type === "feature-cards") {
    return featuresTemplate(section);
  }

  return `
    <section class="section-card" style="padding:24px;">
      <strong>${escapeHtml(section.type)}</strong>
      <p style="margin-top:8px;color:var(--text-soft);">No HTML renderer registered for this section yet.</p>
    </section>
  `;
}
