import { createElement } from "react";
import type { MenusPayload, PageRegionColumn, PageRegionRow, PageRegions, SettingsPayload, SitePayload } from "@/lib/types";
import { buildElementProps } from "@/lib/sections/element-props";
import { SectionRenderer } from "@/components/sections/section-renderer";

type RegionLayoutRendererProps = {
  regions: PageRegions;
  site: SitePayload["site"];
  settings: SettingsPayload["settings"];
  menus: MenusPayload;
};

function columnWidthPercent(column: PageRegionColumn): number {
  const width = Number(column?.width || 12);
  return Math.max(1, Math.min(12, width)) / 12 * 100;
}

function columnClassNames(column: PageRegionColumn): string {
  const classes: string[] = [];
  const xs = column?.width;

  if (xs) {
    classes.push(`col-${Number(xs)}`);
  }

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

function renderColumn(
  column: PageRegionColumn,
  site: SitePayload["site"],
  settings: SettingsPayload["settings"],
  menus: MenusPayload,
) {
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

  const blocks = (column.blocks || [])
    .filter((block) => block.is_active !== false)
    .map((block) => <SectionRenderer key={block.id} section={block} site={site} settings={settings} menus={menus} />);

  const hasColumnChrome = Boolean(
    column.css_class ||
    column.element_id ||
    column.inline_style ||
    column.custom_attributes ||
    (column.responsive?.sm || column.responsive?.md || column.responsive?.lg || column.responsive?.xl) ||
    column?.width,
  );

  if (!hasColumnChrome) {
    return blocks;
  }

  return createElement(
    "div",
    {
      key: column.id,
      ...props,
      style,
    },
    blocks,
  );
}

function renderRow(
  row: PageRegionRow,
  site: SitePayload["site"],
  settings: SettingsPayload["settings"],
  menus: MenusPayload,
) {
  if (row.is_active === false) {
    return null;
  }

  const tag = row.wrapper_tag || "div";
  const className = [row.container, row.css_class].filter(Boolean).join(" ");
  const props = buildElementProps({
    className,
    id: row.element_id,
    inlineStyle: row.inline_style,
    customAttributes: row.custom_attributes,
  });

  const rowChildren = (row.columns || []).map((column) => renderColumn(column, site, settings, menus));
  const hasRowChrome = Boolean(
    row.container ||
    row.wrapper_tag ||
    row.css_class ||
    row.element_id ||
    row.inline_style ||
    row.custom_attributes,
  );

  if (!hasRowChrome) {
    return rowChildren;
  }

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
    rowChildren,
  );
}

export function RegionLayoutRenderer({ regions, site, settings, menus }: RegionLayoutRendererProps) {
  return (
    <>
      {(["header", "body", "footer"] as const).flatMap((region) =>
        (regions[region] || []).map((row) => renderRow(row, site, settings, menus)),
      )}
    </>
  );
}
