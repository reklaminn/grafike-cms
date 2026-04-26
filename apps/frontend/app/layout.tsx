import type { Metadata } from "next";
import "./globals.css";
import Script from "next/script";
import { SiteShell } from "@/components/layout/site-shell";
import { getSettingsPayload, getSitePayload } from "@/lib/api/client";
import { buildTokenStyle } from "@/lib/theme/tokens";
import { canonicalUrl } from "@/lib/seo";

export async function generateMetadata(): Promise<Metadata> {
  const [sitePayload, settingsPayload] = await Promise.all([
    getSitePayload(),
    getSettingsPayload(),
  ]);

  const site     = sitePayload.site;
  const settings = settingsPayload.settings;
  const siteName = settings.site_title || site.name;
  const logoUrl  = settings.logo_url || null;

  return {
    metadataBase: new URL(process.env.NEXT_PUBLIC_SITE_URL ?? "http://localhost:3000"),
    title: {
      default: siteName,
      template: `%s | ${siteName}`,
    },
    description: "",
    openGraph: {
      siteName,
      type: "website",
      locale: site.locale ?? "tr_TR",
      ...(logoUrl ? { images: [{ url: logoUrl, width: 1200, height: 630, alt: siteName }] } : {}),
    },
    twitter: {
      card: "summary_large_image",
    },
    icons: {
      icon: settings.favicon_url || undefined,
    },
  };
}

export default async function RootLayout({
  children,
}: Readonly<{ children: React.ReactNode }>) {
  const sitePayload = await getSitePayload();
  const tokenStyle  = buildTokenStyle(sitePayload.site.tokens);
  const themeCssAssets = sitePayload.site.theme.assets?.css || [];
  const themeJsAssets  = sitePayload.site.theme.assets?.js  || [];

  return (
    <html lang={sitePayload.site.locale?.split("_")[0] ?? "tr"}>
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
