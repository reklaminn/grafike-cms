import "./globals.css";
import Script from "next/script";
import { SiteShell } from "@/components/layout/site-shell";
import { getSitePayload } from "@/lib/api/client";
import { buildTokenStyle } from "@/lib/theme/tokens";

export const metadata = {
  title: "Grafike Frontend",
  description: "Next.js frontend for Grafike Laravel CMS"
};

export default async function RootLayout({
  children
}: Readonly<{ children: React.ReactNode }>) {
  const sitePayload = await getSitePayload();
  const tokenStyle = buildTokenStyle(sitePayload.site.tokens);
  const themeCssAssets = sitePayload.site.theme.assets?.css || [];
  const themeJsAssets = sitePayload.site.theme.assets?.js || [];

  return (
    <html lang="tr">
      <head>
        {themeCssAssets.map((href) => (
          <link key={href} rel="stylesheet" href={href} />
        ))}
      </head>
      <body style={tokenStyle}>
        <SiteShell>{children}</SiteShell>
        {themeJsAssets.map((src) => (
          <Script key={src} src={src} strategy="afterInteractive" />
        ))}
      </body>
    </html>
  );
}
