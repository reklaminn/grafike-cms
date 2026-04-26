/**
 * SEO helpers for Next.js metadata generation.
 *
 * Canonical URL base comes from NEXT_PUBLIC_SITE_URL env var.
 * Falls back to an empty string so relative paths still work.
 */

const SITE_URL = (process.env.NEXT_PUBLIC_SITE_URL ?? "").replace(/\/$/, "");

export function canonicalUrl(path: string): string {
  const cleanPath = path.startsWith("/") ? path : `/${path}`;
  return `${SITE_URL}${cleanPath}`;
}

export type SeoMeta = {
  title: string;
  description: string;
  canonical?: string;
  noindex?: boolean;
  ogImage?: string | null;
  ogType?: "website" | "article";
  publishedTime?: string | null;
  modifiedTime?: string | null;
  authorName?: string | null;
};

export function buildMetadata(meta: SeoMeta) {
  const {
    title,
    description,
    canonical,
    noindex = false,
    ogImage,
    ogType = "website",
    publishedTime,
    modifiedTime,
    authorName,
  } = meta;

  const robots = noindex
    ? { index: false, follow: false }
    : { index: true, follow: true };

  return {
    title,
    description,
    robots,
    alternates: canonical ? { canonical } : undefined,
    openGraph: {
      title,
      description,
      type: ogType,
      url: canonical,
      ...(ogImage ? { images: [{ url: ogImage, width: 1200, height: 630, alt: title }] } : {}),
      ...(publishedTime ? { publishedTime } : {}),
      ...(modifiedTime ? { modifiedTime } : {}),
      ...(authorName ? { authors: [authorName] } : {}),
    },
    twitter: {
      card: ogImage ? ("summary_large_image" as const) : ("summary" as const),
      title,
      description,
      ...(ogImage ? { images: [ogImage] } : {}),
    },
  };
}
