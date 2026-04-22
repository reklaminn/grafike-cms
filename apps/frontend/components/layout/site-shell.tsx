import Link from "next/link";
import type { MenuPayload, SettingsPayload, SitePayload } from "@/lib/types";

type SiteShellProps = {
  site: SitePayload["site"];
  headerMenu: MenuPayload | null;
  settings: SettingsPayload["settings"];
  children: React.ReactNode;
};

export function SiteShell({ site, headerMenu, settings, children }: SiteShellProps) {
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
        {headerMenu?.items?.length ? (
          <nav
            className="section-card"
            style={{
              marginTop: 12,
              padding: "14px 20px",
              display: "flex",
              gap: 18,
              flexWrap: "wrap",
              alignItems: "center"
            }}
          >
            {headerMenu.items.map((item) => (
              <Link key={item.id} href={item.url} style={{ fontWeight: 600 }}>
                {item.title}
              </Link>
            ))}
          </nav>
        ) : null}
      </header>
      {children}
      <footer className="container" style={{ padding: "0 0 32px" }}>
        <div className="section-card" style={{ padding: "18px 20px", color: "var(--text-soft)" }}>
          <div>{settings.footer_text || site.name}</div>
          <div style={{ marginTop: 8, fontSize: 14 }}>
            {settings.contact.email}
          </div>
        </div>
      </footer>
    </div>
  );
}
