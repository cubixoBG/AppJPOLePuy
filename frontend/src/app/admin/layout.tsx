import Link from "next/link";
import styles from "./admin.module.css";

const navLinks = [
  { href: "/admin",               label: "Tableau de bord", icon: "◻" },
  { href: "/admin/visiteurs",     label: "Visiteurs",        icon: "👥" },
  { href: "/admin/planning",      label: "Planning immersion", icon: "📅" },
  { href: "/admin/stats",         label: "Statistiques",     icon: "📊" },
  { href: "/admin/notification",  label: "Notifications",    icon: "🔔" },
];

export default function AdminLayout({ children }: { children: React.ReactNode }) {
  return (
    <div className={styles.layout}>
      {/* Sidebar */}
      <aside className={styles.sidebar}>
        <div className={styles.sidebarHeader}>
          <div className={styles.sidebarLogo}>
            <div className={styles.logoMark}>IUT</div>
            <div>
              <span className={styles.sidebarBrand}>IUT Clermont</span>
              <span className={styles.sidebarSub}>Administration JPO</span>
            </div>
          </div>
        </div>

        <nav className={styles.nav}>
          {navLinks.map((l) => (
            <Link key={l.href} href={l.href} className={styles.navLink}>
              <span className={styles.navIcon}>{l.icon}</span>
              {l.label}
            </Link>
          ))}
        </nav>

        <div className={styles.sidebarFooter}>
          <Link href="/login" className={styles.logoutBtn}>
            ← Se déconnecter
          </Link>
        </div>
      </aside>

      {/* Content */}
      <div className={styles.contentArea}>
        {/* Top bar */}
        <header className={styles.topbar}>
          <span className={styles.topbarTitle}>Journée Portes Ouvertes 2026</span>
          <div className={styles.topbarRight}>
            <span className={styles.adminBadge}>Admin</span>
          </div>
        </header>
        <main className={styles.main}>{children}</main>
      </div>
    </div>
  );
}
