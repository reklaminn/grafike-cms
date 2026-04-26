import type { NextConfig } from "next";

const nextConfig: NextConfig = {
  images: {
    remotePatterns: [
      // Production: HTTPS from any domain (CDN, Spatie storage, etc.)
      { protocol: "https", hostname: "**" },
      // Local dev: HTTP from any hostname
      { protocol: "http",  hostname: "**" },
    ]
  }
};

export default nextConfig;
