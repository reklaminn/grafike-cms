import { headers } from "next/headers";
import type { MenuPayload, MenusPayload, PagePayload, SettingsPayload, SitePayload } from "@/lib/types";
import {
  mockHeaderMenuPayload,
  mockPagePayload,
  mockSettingsPayload,
  mockSitePayload
} from "@/lib/api/mock-data";

const API_BASE_URL = process.env.CMS_API_URL;

type ResourceEnvelope<T> = {
  data: T;
};

function unwrapResource<T>(payload: T | ResourceEnvelope<T>): T {
  if (payload && typeof payload === "object" && "data" in payload) {
    return (payload as ResourceEnvelope<T>).data;
  }

  return payload as T;
}

async function getSiteHostHeader(): Promise<string | null> {
  const requestHeaders = await headers();
  return requestHeaders.get("x-forwarded-host") ?? requestHeaders.get("host");
}

async function fetchJson<T>(path: string, fallback: T, wrapped = true): Promise<T> {
  if (!API_BASE_URL) {
    return fallback;
  }

  try {
    const siteHost = await getSiteHostHeader();
    const response = await fetch(`${API_BASE_URL}${path}`, {
      headers: siteHost ? { "X-Site-Host": siteHost } : undefined,
      next: { revalidate: 60 }
    });

    if (!response.ok) {
      return fallback;
    }

    const payload = (await response.json()) as T | ResourceEnvelope<T>;
    return wrapped ? unwrapResource<T>(payload) : (payload as T);
  } catch {
    return fallback;
  }
}

export async function getSitePayload(): Promise<SitePayload> {
  return fetchJson<SitePayload>("/api/v1/site", mockSitePayload);
}

export async function getSettingsPayload(): Promise<SettingsPayload> {
  return fetchJson<SettingsPayload>("/api/v1/settings", mockSettingsPayload, false);
}

export async function getMenuPayload(location: string): Promise<MenuPayload> {
  return fetchJson<MenuPayload>(`/api/v1/menus/${location}`, mockHeaderMenuPayload);
}

export async function getMenusPayload(): Promise<MenusPayload> {
  return fetchJson<MenusPayload>("/api/v1/menus", [mockHeaderMenuPayload]);
}

export async function getPagePayload(slug: string): Promise<PagePayload | null> {
  const fallback = mockPagePayload(slug);

  return fetchJson<PagePayload | null>(`/api/v1/pages/${slug}`, fallback);
}
