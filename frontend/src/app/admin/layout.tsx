import Link from "next/link";

const navLinks = [
  { href: "/admin",              label: "Tableau de bord" },
  { href: "/admin/visiteurs",    label: "Visiteurs" },
  { href: "/admin/planning",     label: "Planning immersion" },
  { href: "/admin/stats",        label: "Statistiques" },
  { href: "/admin/notification", label: "Notifications" },
];

export default function AdminLayout({ children }: { children: React.ReactNode }) {
  return (
    <div>
      <header>
        <div>
          <svg viewBox="0 0 40 40" fill="none" width="36" height="36">
            <path d="M20 3L5 9v10c0 9 6.5 17.4 15 19.5C29.5 36.4 36 28 36 19V9L20 3z" fill="rgba(255,255,255,0.15)" stroke="rgba(255,255,255,0.6)" strokeWidth="1.5" />
            <text x="20" y="24" textAnchor="middle" fill="white" fontSize="9" fontWeight="700" fontFamily="sans-serif">IUT</text>
          </svg>
          <div>
            <span>IUT Clermont-Ferrand</span>
            <span>Université Clermont Auvergne</span>
          </div>
        </div>
        <Link href="/">Retour au site</Link>
      </header>

      <main>{children}</main>
    </div>
  );
}
