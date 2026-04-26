import { notFound } from "next/navigation";
import { RegionLayoutRenderer } from "@/components/sections/region-layout-renderer";
import { SectionRenderer } from "@/components/sections/section-renderer";
import { getMenusPayload, getPagePayload, getSettingsPayload, getSitePayload } from "@/lib/api/client";
import { getRenderableSections } from "@/lib/sections/region-sections";

type CatchAllPageProps = {
  params: Promise<{ slug?: string[] }>;
};

export default async function CatchAllPage({ params }: CatchAllPageProps) {
  const resolvedParams = await params;
  const slug = resolvedParams.slug?.join("/") || "home";
  const [payload, sitePayload, settingsPayload, menusPayload] = await Promise.all([
    getPagePayload(slug),
    getSitePayload(),
    getSettingsPayload(),
    getMenusPayload(),
  ]);

  if (!payload?.page) {
    notFound();
  }

  const pageId = payload.page.id;
  const lang   = sitePayload.site.locale?.split("_")[0] ?? "tr";

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
