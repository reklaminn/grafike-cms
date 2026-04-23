import type { PageRegionBlock, PageRegions, PageSection } from "@/lib/types";

export function flattenRegionBlocks(regions?: PageRegions): PageRegionBlock[] {
  if (!regions) {
    return [];
  }

  return (["header", "body", "footer"] as const).flatMap((region) =>
    (regions[region] || [])
      .filter((row) => row.is_active !== false)
      .flatMap((row) =>
        (row.columns || [])
          .filter((column) => column.is_active !== false)
          .flatMap((column) =>
            (column.blocks || [])
              .filter((block) => block.is_active !== false)
              .map((block) => ({
                ...block,
                region,
                row_id: row.id,
                column_id: column.id,
                column_width: column.width,
              })),
          ),
      ),
  );
}

export function getRenderableSections(
  sections?: PageSection[],
  regions?: PageRegions,
): Array<PageSection | PageRegionBlock> {
  const regionBlocks = flattenRegionBlocks(regions);

  if (regionBlocks.length > 0) {
    return regionBlocks;
  }

  return (sections || []).filter((section) => section.is_active !== false);
}
