import { notFound } from "next/navigation";
import { RegionLayoutRenderer } from "@/components/sections/region-layout-renderer";
import { SectionRenderer } from "@/components/sections/section-renderer";
import { getPagePayload } from "@/lib/api/client";
import { getRenderableSections } from "@/lib/sections/region-sections";

type CatchAllPageProps = {
  params: Promise<{ slug?: string[] }>;
};

export default async function CatchAllPage({ params }: CatchAllPageProps) {
  const resolvedParams = await params;
  const slug = resolvedParams.slug?.join("/") || "home";
  const payload = await getPagePayload(slug);

  if (!payload?.page) {
    notFound();
  }

  if (payload.page.regions) {
    return (
      <main className="page-stack">
        <RegionLayoutRenderer regions={payload.page.regions} />
      </main>
    );
  }

  const sections = getRenderableSections(payload.page.sections, payload.page.regions);

  return (
    <main className="container page-stack">
      {sections.map((section) => (
        <SectionRenderer key={section.id} section={section} />
      ))}
    </main>
  );
}
