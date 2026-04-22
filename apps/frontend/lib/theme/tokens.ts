import type { CSSProperties } from "react";
import type { ThemeTokens } from "@/lib/types";

const tokenToCssVariableMap: Record<string, string> = {
  color_primary: "--color-primary",
  color_secondary: "--color-secondary",
  color_accent: "--color-accent",
  radius_card: "--radius-card",
  radius_button: "--radius-button",
  container_width: "--container-width"
};

type TokenStyle = CSSProperties & Record<`--${string}`, string>;

export function buildTokenStyle(tokens: ThemeTokens): TokenStyle {
  return Object.entries(tokens).reduce<TokenStyle>((styles, [key, value]) => {
    const cssVariable = tokenToCssVariableMap[key];

    if (cssVariable) {
      styles[cssVariable as `--${string}`] = value;
    }

    return styles;
  }, {} as TokenStyle);
}
