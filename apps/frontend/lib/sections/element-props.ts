import type { CSSProperties, HTMLAttributes } from "react";

type ElementOptions = {
  className?: string | null;
  id?: string | null;
  inlineStyle?: string | null;
  customAttributes?: string | null;
};

function toCamelCase(value: string): string {
  return value.replace(/-([a-z])/g, (_match, char: string) => char.toUpperCase());
}

export function parseInlineStyle(styleText?: string | null): CSSProperties | undefined {
  if (!styleText) {
    return undefined;
  }

  const style = styleText
    .split(";")
    .map((rule) => rule.trim())
    .filter(Boolean)
    .reduce<Record<string, string>>((acc, rule) => {
      const [property, ...valueParts] = rule.split(":");

      if (!property || valueParts.length === 0) {
        return acc;
      }

      acc[toCamelCase(property.trim())] = valueParts.join(":").trim();
      return acc;
    }, {});

  return Object.keys(style).length > 0 ? (style as CSSProperties) : undefined;
}

export function parseCustomAttributes(attributeText?: string | null): HTMLAttributes<HTMLElement> {
  if (!attributeText) {
    return {};
  }

  const attributes: Record<string, string> = {};
  const pattern = /([^\s=]+)(?:=(?:"([^"]*)"|'([^']*)'|([^\s]+)))?/g;

  for (const match of attributeText.matchAll(pattern)) {
    const [, key, doubleQuoted, singleQuoted, bareValue] = match;
    attributes[key] = doubleQuoted ?? singleQuoted ?? bareValue ?? "";
  }

  return attributes;
}

export function buildElementProps({ className, id, inlineStyle, customAttributes }: ElementOptions) {
  return {
    ...(className ? { className } : {}),
    ...(id ? { id } : {}),
    ...(parseInlineStyle(inlineStyle) ? { style: parseInlineStyle(inlineStyle) } : {}),
    ...parseCustomAttributes(customAttributes),
  };
}
