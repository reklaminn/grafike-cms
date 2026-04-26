/**
 * Renders article content_json blocks as React components.
 * Mirrors the PHP ArticleBlockRenderer but for the Next.js frontend.
 *
 * Block types: heading | paragraph | image (gallery) | video | html
 */
import Image from "next/image";
import type { ArticleBlock } from "@/lib/types";

// ─── Individual block components ─────────────────────────────────────────────

function HeadingBlock({ block }: { block: ArticleBlock }) {
  const level = Math.max(1, Math.min(6, block.level ?? 2));
  const Tag = `h${level}` as "h1" | "h2" | "h3" | "h4" | "h5" | "h6";
  return <Tag style={{ marginTop: "1.5rem", marginBottom: "0.5rem" }}>{block.text}</Tag>;
}

function ParagraphBlock({ block }: { block: ArticleBlock }) {
  if (!block.content) return null;
  return (
    <div
      className="article-paragraph"
      // Paragraph blocks store rich HTML from Quill — rendered as-is
      // biome-ignore lint/security/noDangerouslySetInnerHtml: CMS-controlled rich text
      dangerouslySetInnerHTML={{ __html: block.content }}
      style={{ marginTop: "1rem", lineHeight: 1.7 }}
    />
  );
}

function SingleFigure({
  url,
  alt,
  caption,
}: {
  url: string;
  alt?: string;
  caption?: string;
}) {
  return (
    <figure style={{ margin: "1.5rem 0" }}>
      {/* Use unoptimized for editor-uploaded images with unknown dimensions */}
      <Image
        src={url}
        alt={alt ?? ""}
        width={1200}
        height={800}
        unoptimized
        style={{ maxWidth: "100%", height: "auto", borderRadius: "0.5rem", display: "block" }}
      />
      {caption && (
        <figcaption
          style={{
            marginTop: "0.4rem",
            fontSize: "0.8rem",
            color: "var(--color-text-soft, #6b7280)",
            textAlign: "center",
          }}
        >
          {caption}
        </figcaption>
      )}
    </figure>
  );
}

function ImageBlock({ block }: { block: ArticleBlock }) {
  // Normalise: v2 images[] OR legacy single url
  const images: Array<{ url: string; alt?: string; caption?: string }> =
    Array.isArray(block.images) && block.images.length > 0
      ? block.images.filter((i) => i.url?.trim())
      : block.url?.trim()
        ? [{ url: block.url, alt: block.alt, caption: block.caption }]
        : [];

  if (images.length === 0) return null;

  if (images.length === 1) {
    return <SingleFigure {...images[0]} />;
  }

  return (
    <div
      className="article-gallery"
      style={{
        display: "grid",
        gridTemplateColumns: "repeat(auto-fill, minmax(200px, 1fr))",
        gap: "0.75rem",
        margin: "1.5rem 0",
      }}
    >
      {images.map((img, i) => (
        // biome-ignore lint/suspicious/noArrayIndexKey: static content list
        <SingleFigure key={i} {...img} />
      ))}
    </div>
  );
}

function VideoBlock({ block }: { block: ArticleBlock }) {
  if (block.embed_url) {
    return (
      <div
        style={{
          position: "relative",
          paddingBottom: "56.25%",
          height: 0,
          overflow: "hidden",
          margin: "1.5rem 0",
          borderRadius: "0.5rem",
        }}
      >
        <iframe
          src={block.embed_url}
          frameBorder="0"
          allowFullScreen
          loading="lazy"
          title="Video"
          style={{ position: "absolute", top: 0, left: 0, width: "100%", height: "100%" }}
        />
      </div>
    );
  }

  if (block.url) {
    return (
      <video
        src={block.url}
        controls
        style={{ maxWidth: "100%", borderRadius: "0.5rem", margin: "1.5rem 0" }}
      />
    );
  }

  return null;
}

function HtmlBlock({ block }: { block: ArticleBlock }) {
  if (!block.code) return null;
  return (
    <div
      className="article-html-block"
      // biome-ignore lint/security/noDangerouslySetInnerHtml: CMS-controlled embed
      dangerouslySetInnerHTML={{ __html: block.code }}
      style={{ margin: "1.5rem 0" }}
    />
  );
}

// ─── Main renderer ────────────────────────────────────────────────────────────

type ArticleBlockRendererProps = {
  blocks: ArticleBlock[];
};

export function ArticleBlockRenderer({ blocks }: ArticleBlockRendererProps) {
  if (!blocks || blocks.length === 0) return null;

  return (
    <div className="article-content">
      {blocks.map((block, index) => {
        switch (block.type) {
          case "heading":
            return <HeadingBlock key={index} block={block} />;
          case "paragraph":
            return <ParagraphBlock key={index} block={block} />;
          case "image":
            return <ImageBlock key={index} block={block} />;
          case "video":
            return <VideoBlock key={index} block={block} />;
          case "html":
            return <HtmlBlock key={index} block={block} />;
          default:
            return null;
        }
      })}
    </div>
  );
}
