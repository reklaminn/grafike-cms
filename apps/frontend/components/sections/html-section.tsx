import { Fragment, createElement } from "react";
import { parse } from "node-html-parser";
import { parseInlineStyle } from "@/lib/sections/element-props";

type HtmlSectionProps = {
  html: string;
};

const VOID_TAGS = new Set([
  "area",
  "base",
  "br",
  "col",
  "embed",
  "hr",
  "img",
  "input",
  "link",
  "meta",
  "param",
  "source",
  "track",
  "wbr",
]);

function normalizeAttributeName(name: string): string {
  if (name === "class") {
    return "className";
  }

  if (name === "for") {
    return "htmlFor";
  }

  if (name === "style") {
    return "style";
  }

  if (name.startsWith("data-") || name.startsWith("aria-")) {
    return name;
  }

  if (name === "xlink:href") {
    return "xlinkHref";
  }

  if (name === "xmlns:xlink") {
    return "xmlnsXlink";
  }

  if (name.includes(":")) {
    return name.replace(/:([a-zA-Z])/g, (_match, char: string) => char.toUpperCase());
  }

  if (name.includes("-")) {
    return name.replace(/-([a-zA-Z])/g, (_match, char: string) => char.toUpperCase());
  }

  return name;
}

function toReactNodes(html: string) {
  const root = parse(html, {
    comment: false,
    lowerCaseTagName: false,
    blockTextElements: {
      script: true,
      noscript: true,
      style: true,
      pre: true,
    },
  });

  const renderNode = (node: any, key: string): React.ReactNode => {
    if (!node) return null;

    if (node.nodeType === 3) {
      return node.rawText;
    }

    if (node.nodeType !== 1) {
      return null;
    }

    const tag = String(node.rawTagName || node.tagName || "div");
    const attrs = node.attributes || {};
    const props: Record<string, unknown> = { key };

    Object.entries(attrs).forEach(([name, value]) => {
      if (name === "class") {
        props.className = value;
        return;
      }

      if (name === "style") {
        props.style = parseInlineStyle(String(value));
        return;
      }

      props[normalizeAttributeName(name)] = value;
    });

    if (VOID_TAGS.has(tag.toLowerCase())) {
      return createElement(tag, props);
    }

    const children = (node.childNodes || [])
      .map((child: any, index: number) => renderNode(child, `${key}-${index}`))
      .filter((child: React.ReactNode) => child !== null && child !== undefined);

    return createElement(tag, props, ...children);
  };

  return (root.childNodes || []).map((node: any, index: number) => renderNode(node, `html-node-${index}`));
}

export function HtmlSection({ html }: HtmlSectionProps) {
  return <Fragment>{toReactNodes(html)}</Fragment>;
}
