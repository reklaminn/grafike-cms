import "./globals.css";
import { SiteShell } from "@/components/layout/site-shell";
import { getMenuPayload, getSettingsPayload, getSitePayload } from "@/lib/api/client";
import { buildTokenStyle } from "@/lib/theme/tokens";

export const metadata = {
  title: "Grafike Frontend",
  description: "Next.js frontend for Grafike Laravel CMS"
};

export default async function RootLayout({
  children
}: Readonly<{ children: React.ReactNode }>) {
  const [sitePayload, headerMenu, settingsPayload] = await Promise.all([
    getSitePayload(),
    getMenuPayload("header"),
    getSettingsPayload()
  ]);
  const tokenStyle = buildTokenStyle(sitePayload.site.tokens);

  return (
    <html lang="tr">
      <body style={tokenStyle}>
        <SiteShell
          site={sitePayload.site}
          headerMenu={headerMenu}
          settings={settingsPayload.settings}
        >
          {children}
        </SiteShell>
      </body>
    </html>
  );
}
