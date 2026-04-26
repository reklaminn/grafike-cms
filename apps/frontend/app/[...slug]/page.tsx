import type { Metadata } from "next";
import { notFound } from "next/navigation";
import Link from "next/link";
import { RegionLayoutRenderer } from "@/components/sections/region-layout-renderer";
import { SectionRenderer } from "@/components/sections/section-renderer";
import { ArticleBlockRenderer } from "@/components/articles/article-block-renderer";
import {
  getArticle,
  getMenusPayload,
  getPagePayload,
  getSettingsPayload,
  getSitePayload,
} from "@/lib/api/client";
import { getRenderableSections } from "@/lib/sections/region-sections";

type CatchAllPageProps = {
  params: Promise<{ slug?: string[] }>;
};

// ─── Metadata ─────────────────────────────────────────────────────────────────

export async function generateMetadata({ params }: CatchAllPageProps): Promise<Metadata> {
  const resolvedParams = await params;
  const segments = resolvedParams.slug ?? [];
  const slug = segments.join("/") || "home";

  // Try page first
  const payload = await getPagePayload(slug);
  if (payload?.seo) {
    return {
      title: payload.seo.title,
      description: payload.seo.description,
    };
  }

  // Try article (last segment)
  const lastSegment = segments[segments.length - 1];
  if (lastSegment) {
    const detail = await getArticle(lastSegment);
    if (detail) {
      return {
        title: detail.seo?.title ?? detail.article.title,
        description: detail.seo?.description ?? detail.article.excerpt ?? "",
      };
    }
  }

  return {};
}

// ─── Page ─────────────────────────────────────────────────────────────────────

export default async function CatchAllPage({ params }: CatchAllPageProps) {
  const resolvedParams = await params;
  const segments = resolvedParams.slug ?? [];
  const slug = segments.join("/") || "home";

  const [sitePayload, settingsPayload, menusPayload] = await Promise.all([
    getSitePayload(),
    getSettingsPayload(),
    getMenusPayload(),
  ]);

  const lang = sitePayload.site.locale?.split("_")[0] ?? "tr";

  // ── 1. Try as a Page ──────────────────────────────────────────────────
  const payload = await getPagePayload(slug);

  if (payload?.page) {
    const pageId = payload.page.id;

    if (payload.page.regions) {
      return (
        <main className="page-stack">
          <RegionLayoutRenderer
            regions={payload.page.regions}
            site={sitePayload.site}
            settings={settingsPayload.settings}
            menus={menusPayload}
            pageId={pageId}
            lang={lang}
          />
        </main>
      );
    }

    const sections = getRenderableSections(payload.page.sections, payload.page.regions);

    return (
      <main className="container page-stack">
        {sections.map((section) => (
          <SectionRenderer
            key={section.id}
            section={section}
            site={sitePayload.site}
            settings={settingsPayload.settings}
            menus={menusPayload}
            pageId={pageId}
            lang={lang}
          />
        ))}
      </main>
    );
  }

  // ── 2. Try as Article detail (last path segment = article slug) ───────
  const articleSlug = segments[segments.length - 1];
  if (!articleSlug) notFound();

  const detail = await getArticle(articleSlug);
  if (!detail) notFound();

  const { article, author, page: articlePage } = detail;

  // Parent page slug for "back to list" link
  const parentSlug = articlePage?.slug ?? (segments.length > 1 ? segments.slice(0, -1).join("/") : null);

  return (
    <main className="container" style={{ maxWidth: "780px", margin: "0 auto", padding: "2rem 1rem" }}>
      {/* Breadcrumb */}
      {parentSlug && (
        <nav style={{ marginBottom: "1.5rem", fontSize: "0.85rem", color: "var(--color-text-soft, #6b7280)" }}>
          <Link href="/" style={{ color: "inherit", textDecoration: "none" }}>Ana Sayfa</Link>
          {" / "}
          <Link href={`/${parentSlug}`} style={{ color: "inherit", textDecoration: "none" }}>
            {articlePage?.title ?? parentSlug}
          </Link>
          {" / "}
          <span>{article.title}</span>
        </nav>
      )}

      {/* Cover image */}
      {article.cover?.url && (
        <div style={{ marginBottom: "2rem", borderRadius: "0.75rem", overflow: "hidden", aspectRatio: "16/9" }}>
          <img
            src={article.cover.url}
            alt={article.cover.alt ?? article.title}
            style={{ width: "100%", height: "100%", objectFit: "cover", display: "block" }}
          />
        </div>
      )}

      {/* Header */}
      <header style={{ marginBottom: "2rem" }}>
        <h1 style={{ fontSize: "2rem", fontWeight: 700, lineHeight: 1.25, marginBottom: "1rem" }}>
          {article.title}
        </h1>

        <div
          style={{
            display: "flex",
            gap: "1rem",
            alignItems: "center",
            fontSize: "0.875rem",
            color: "var(--color-text-soft, #6b7280)",
            flexWrap: "wrap",
          }}
        >
          {author?.name && (
            <span>
              <strong style={{ color: "var(--color-heading, inherit)" }}>{author.name}</strong>
            </span>
          )}
          {article.display_date && (
            <time dateTime={article.display_date}>
              {new Date(article.display_date).toLocaleDateString("tr-TR", {
                day: "numeric",
                month: "long",
                year: "numeric",
              })}
            </time>
          )}
        </div>

        {article.excerpt && (
          <p
            style={{
              marginTop: "1rem",
              fontSize: "1.1rem",
              color: "var(--color-text-soft, #374151)",
              lineHeight: 1.6,
              fontStyle: "italic",
              borderLeft: "3px solid var(--color-primary, #6366f1)",
              paddingLeft: "1rem",
            }}
          >
            {article.excerpt}
          </p>
        )}
      </header>

      {/* Content — prefer content_json blocks, fall back to body HTML */}
      {article.content_json && article.content_json.length > 0 ? (
        <ArticleBlockRenderer blocks={article.content_json} />
      ) : article.body ? (
        <div
          className="article-content"
          // biome-ignore lint/security/noDangerouslySetInnerHtml: CMS-controlled body HTML
          dangerouslySetInnerHTML={{ __html: article.body }}
          style={{ lineHeight: 1.7 }}
        />
      ) : null}

      {/* Back link */}
      {parentSlug && (
        <div style={{ marginTop: "3rem", paddingTop: "2rem", borderTop: "1px solid var(--color-border, #e5e7eb)" }}>
          <Link
            href={`/${parentSlug}`}
            style={{
              color: "var(--color-primary, #6366f1)",
              fontWeight: 600,
              textDecoration: "none",
              fontSize: "0.9rem",
            }}
          >
            ← {articlePage?.title ?? "Listeye dön"}
          </Link>
        </div>
      )}
    </main>
  );
}
