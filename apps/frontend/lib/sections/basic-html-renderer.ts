import type { MenuItem, MenusPayload, PageSection, SettingsPayload, SitePayload } from "@/lib/types";

function escapeHtml(value: unknown): string {
  return String(value ?? "")
    .replaceAll("&", "&amp;")
    .replaceAll("<", "&lt;")
    .replaceAll(">", "&gt;")
    .replaceAll('"', "&quot;")
    .replaceAll("'", "&#039;");
}

export type SectionRenderContext = {
  site: SitePayload["site"];
  settings: SettingsPayload["settings"];
  menus: MenusPayload;
};

type RepeaterSchema = {
  type?: unknown;
  item_template?: unknown;
};

function renderMenuItems(items: MenuItem[]): string {
  return items
    .map((item) => {
      const children = item.children?.length
        ? `<ul>${renderMenuItems(item.children)}</ul>`
        : "";

      return `<li><a href="${escapeHtml(item.url)}"${item.target ? ` target="${escapeHtml(item.target)}"` : ""}>${escapeHtml(item.title)}</a>${children}</li>`;
    })
    .join("");
}

function buildSystemPlaceholders(context: SectionRenderContext): Record<string, string> {
  const { site, settings, menus } = context;
  const placeholders: Record<string, string> = {
    site_name: site.name,
    site_domain: site.domain,
    site_locale: site.locale || "",
    theme_slug: site.theme.slug,
    header_variant: site.header_variant || "",
    footer_variant: site.footer_variant || "",
    site_title: settings.site_title || site.name,
    logo_url: settings.logo_url || "",
    favicon_url: settings.favicon_url || "",
    footer_text: settings.footer_text || "",
    contact_phone: settings.contact.phone || "",
    phone: settings.contact.phone || "",
    contact_email: settings.contact.email || "",
    email: settings.contact.email || "",
    contact_address: settings.contact.address || "",
    address: settings.contact.address || "",
    social_whatsapp: settings.social?.whatsapp || "",
    whatsapp_number: settings.social?.whatsapp || "",
    social_instagram: settings.social?.instagram || "",
    social_facebook: settings.social?.facebook || "",
    social_x: settings.social?.x || settings.social?.twitter || "",
  };

  menus.forEach((menu) => {
    const keyParts = [menu.location, menu.slug].filter(Boolean);

    keyParts.forEach((key) => {
      placeholders[`menu_${key}_html`] = `<ul>${renderMenuItems(menu.items || [])}</ul>`;
      placeholders[`menu_${key}_items_html`] = renderMenuItems(menu.items || []);
      placeholders[`menu_${key}_name`] = menu.name;
    });
  });

  return placeholders;
}

function resolveValue(
  key: string,
  content: PageSection["content"],
  systemValues: Record<string, string>,
): unknown {
  if (Object.prototype.hasOwnProperty.call(content, key)) {
    return content[key];
  }

  return systemValues[key];
}

function resolveRepeaterHtml(
  key: string,
  content: PageSection["content"],
  systemValues: Record<string, string>,
  context: SectionRenderContext,
  schema?: PageSection["schema"],
): string | null {
  if (!key.endsWith("_html")) {
    return null;
  }

  const baseKey = key.slice(0, -5);
  const value = content[baseKey];
  const fieldSchema = schema?.[baseKey] as RepeaterSchema | undefined;

  if (!Array.isArray(value) || fieldSchema?.type !== "repeater" || typeof fieldSchema.item_template !== "string") {
    return null;
  }

  return value
    .filter((item): item is PageSection["content"] => item !== null && typeof item === "object" && !Array.isArray(item))
    .map((item) => renderTemplateString(fieldSchema.item_template as string, item, context))
    .join("");
}

function renderTemplateString(
  template: string,
  content: PageSection["content"],
  context: SectionRenderContext,
  schema?: PageSection["schema"],
): string {
  const systemValues = buildSystemPlaceholders(context);

  const withRawValues = template.replaceAll(/{{{\s*([a-zA-Z0-9_]+)\s*}}}/g, (_match, key: string) => {
    const repeaterHtml = resolveRepeaterHtml(key, content, systemValues, context, schema);

    if (repeaterHtml !== null) {
      return repeaterHtml;
    }

    return String(resolveValue(key, content, systemValues) ?? "");
  });

  return withRawValues.replaceAll(/{{\s*([a-zA-Z0-9_]+)\s*}}/g, (_match, key: string) => {
    return escapeHtml(resolveValue(key, content, systemValues));
  });
}

export function renderBasicHtmlSection(section: PageSection, context: SectionRenderContext): string {
  const template = section.html_override || section.html_template;

  if (template) {
    return renderTemplateString(template, section.content, context, section.schema);
  }

  return `
    <section class="section-card" style="padding:24px;">
      <strong>${escapeHtml(section.type)}</strong>
      <p style="margin-top:8px;color:var(--text-soft);">No HTML renderer registered for this section yet.</p>
    </section>
  `;
}
