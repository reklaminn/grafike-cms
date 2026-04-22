import type { PagePayload, SitePayload } from "@/lib/types";
import { mockPagePayload, mockSitePayload } from "@/lib/api/mock-data";

const API_BASE_URL = process.env.CMS_API_URL;

async function fetchJson<T>(path: string, fallback: T): Promise<T> {
  if (!API_BASE_URL) {
    return fallback;
  }

  try {
    const response = await fetch(`${API_BASE_URL}${path}`, {
      next: { revalidate: 60 }
    });

    if (!response.ok) {
      return fallback;
    }

    return (await response.json()) as T;
  } catch {
    return fallback;
  }
}

export async function getSitePayload(): Promise<SitePayload> {
  return fetchJson<SitePayload>("/api/site", mockSitePayload);
}

export async function getPagePayload(slug: string): Promise<PagePayload | null> {
  const fallback = mockPagePayload(slug);

  if (!fallback) {
    return null;
  }

  return fetchJson<PagePayload>(`/api/pages/${slug}`, fallback);
}
