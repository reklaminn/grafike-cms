import { createElement } from "react";
import type { PageRegionColumn, PageRegionRow, PageRegions } from "@/lib/types";
import { buildElementProps } from "@/lib/sections/element-props";
import { SectionRenderer } from "@/components/sections/section-renderer";

type RegionLayoutRendererProps = {
  regions: PageRegions;
};

function columnWidthPercent(column: PageRegionColumn): number {
  const width = Number(column?.responsive?.xs || column.width || 12);
  return Math.max(1, Math.min(12, width)) / 12 * 100;
}

function columnClassNames(column: PageRegionColumn): string {
  const classes = [`col-${Number(column?.responsive?.xs || column.width || 12)}`];

  (["sm", "md", "lg", "xl"] as const).forEach((breakpoint) => {
    if (column?.responsive?.[breakpoint]) {
      classes.push(`col-${breakpoint}-${column.responsive[breakpoint]}`);
    }
  });

  if (column.css_class) {
    classes.push(column.css_class);
  }

  return classes.join(" ");
}

function renderColumn(column: PageRegionColumn) {
  if (column.is_active === false) {
    return null;
  }

  const widthPercent = columnWidthPercent(column);
  const props = buildElementProps({
    className: columnClassNames(column),
    id: column.element_id,
    inlineStyle: column.inline_style,
    customAttributes: column.custom_attributes,
  });

  const style = {
    flex: `0 0 ${widthPercent}%`,
    maxWidth: `${widthPercent}%`,
    minWidth: widthPercent >= 100 ? "100%" : "280px",
    boxSizing: "border-box" as const,
    ...(props.style || {}),
  };

  return createElement(
    "div",
    {
      key: column.id,
      ...props,
      style,
    },
    (column.blocks || [])
      .filter((block) => block.is_active !== false)
      .map((block) => <SectionRenderer key={block.id} section={block} />),
  );
}

function renderRow(row: PageRegionRow) {
  if (row.is_active === false) {
    return null;
  }

  const tag = row.wrapper_tag || "section";
  const className = [row.container, row.css_class].filter(Boolean).join(" ");
  const props = buildElementProps({
    className,
    id: row.element_id,
    inlineStyle: row.inline_style,
    customAttributes: row.custom_attributes,
  });

  return createElement(
    tag,
    {
      key: row.id,
      ...props,
      style: {
        display: "flex",
        flexWrap: "wrap",
        gap: "24px",
        width: "100%",
        ...(props.style || {}),
      },
    },
    (row.columns || []).map((column) => renderColumn(column)),
  );
}

export function RegionLayoutRenderer({ regions }: RegionLayoutRendererProps) {
  return (
    <>
      {(["header", "body", "footer"] as const).flatMap((region) =>
        (regions[region] || []).map((row) => renderRow(row)),
      )}
    </>
  );
}
