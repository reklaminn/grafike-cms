import { notFound } from "next/navigation";
import { SectionRenderer } from "@/components/sections/section-renderer";
import { getPagePayload } from "@/lib/api/client";

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

  return (
    <main className="container page-stack">
      {payload.page.sections.filter((section) => section.is_active).map((section) => (
        <SectionRenderer key={section.id} section={section} />
      ))}
    </main>
  );
}
