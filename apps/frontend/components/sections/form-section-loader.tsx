/**
 * Server Component wrapper that fetches form data then renders FormSection.
 * SectionRenderer calls this for sections with type === "form".
 */
import { getForm } from "@/lib/api/client";
import { FormSection } from "@/components/sections/form-section";
import type { PageSection } from "@/lib/types";

type FormSectionLoaderProps = {
  section: PageSection;
};

export async function FormSectionLoader({ section }: FormSectionLoaderProps) {
  const content = section.content as {
    form_id?: number | string;
    title?: string;
    description?: string;
    submit_label?: string;
  };

  if (!content.form_id) {
    return (
      <section style={{ padding: "2rem", textAlign: "center", color: "var(--color-text-soft, #9ca3af)" }}>
        <p>Form ID belirtilmemiş.</p>
      </section>
    );
  }

  const form = await getForm(content.form_id);

  if (!form) {
    return null;
  }

  return (
    <FormSection
      form={form}
      title={content.title}
      description={content.description}
      submitLabel={content.submit_label ?? "Gönder"}
    />
  );
}
