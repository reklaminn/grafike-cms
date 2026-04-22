import "./globals.css";
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

  return (
    <html lang="tr">
      <body style={tokenStyle}>
        <SiteShell site={sitePayload.site}>{children}</SiteShell>
      </body>
    </html>
  );
}
