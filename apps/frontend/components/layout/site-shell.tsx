import type { SitePayload } from "@/lib/types";

type SiteShellProps = {
  site: SitePayload["site"];
  children: React.ReactNode;
};

export function SiteShell({ site, children }: SiteShellProps) {
  return (
    <div className="site-shell">
      <header className="container" style={{ padding: "24px 0 12px" }}>
        <div
          className="section-card"
          style={{
            padding: "18px 20px",
            display: "flex",
            alignItems: "center",
            justifyContent: "space-between",
            background: "var(--color-secondary)"
          }}
        >
          <div>
            <div style={{ fontSize: 24, fontWeight: 800 }}>{site.name}</div>
            <div style={{ color: "var(--text-soft)", fontSize: 14 }}>
              Theme: {site.theme.slug}
            </div>
          </div>
          <div className="dev-note">Basic HTML Section Mode aktif</div>
        </div>
      </header>
      {children}
    </div>
  );
}
