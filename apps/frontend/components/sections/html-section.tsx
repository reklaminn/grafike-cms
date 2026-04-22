type HtmlSectionProps = {
  html: string;
};

export function HtmlSection({ html }: HtmlSectionProps) {
  return <section className="html-section" dangerouslySetInnerHTML={{ __html: html }} />;
}
