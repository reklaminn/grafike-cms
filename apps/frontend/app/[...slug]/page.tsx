import { notFound } from "next/navigation";
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

  const sections = getRenderableSections(payload.page.sections, payload.page.regions);

  return (
    <main className="container page-stack">
      {sections.map((section) => (
        <SectionRenderer key={section.id} section={section} />
      ))}
    </main>
  );
}
