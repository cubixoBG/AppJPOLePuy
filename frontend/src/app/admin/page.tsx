import { useState } from "react";
import Header from "@/components/Header/header";

const STATS = [
  { value: 8, label: "Visiteurs totaux", icon: <IconPerson /> },
  { value: 3, label: "MMI",              icon: <IconMMI /> },
  { value: 3, label: "Informatique",     icon: <IconComputer /> },
  { value: 2, label: "Chimie",           icon: <IconChimie /> },
];

const VISITEURS = [
  { nom: "Martin Lucas",  email: "lucas.martin@mail.com",  dept: "MMI",          etab: "Lycée Blaise Pascal" },
  { nom: "Bernard Emma",  email: "emma.b@mail.com",        dept: "Informatique", etab: "Lycée Jeanne d'Arc" },
  { nom: "Petit Hugo",    email: "hugo.petit@mail.com",    dept: "MMI",          etab: "Lycée Lafayette" },
  { nom: "Dubois Chloé",  email: "chloe.d@mail.com",       dept: "Chimie",       etab: "Lycée Blaise Pascal" },
  { nom: "Moreau Nathan", email: "nathan.m@mail.com",      dept: "Informatique", etab: "Lycée Sidoine Apollinaire" },
  { nom: "Laurent Léa",   email: "lea.laurent@mail.com",   dept: "MMI",          etab: "Lycée Ambroise Brugière" },
  { nom: "Simon Théo",    email: "thao.s@mail.com",        dept: "Chimie",       etab: "Lycée Jeanne d'Arc" },
  { nom: "Michel Manon",  email: "manon.m@mail.com",       dept: "Informatique", etab: "Lycée Lafayette" },
];

const BAR_DATA = [
  { dept: "MMI",          count: 3, color: "#00a89d" },
  { dept: "Informatique", count: 3, color: "#2d6a7f" },
  { dept: "Chimie",       count: 2, color: "#c4a770" },
];

const PIE_DATA = [
  { label: "MMI",          value: 37.5, color: "#00a89d" },
  { label: "Informatique", value: 37.5, color: "#2d6a7f" },
  { label: "Chimie",       value: 12.5, color: "#c4a770" },
  { label: "Autre",        value: 12.5, color: "#d1d5db" },
];

function IconPerson() {
  return (
    <svg viewBox="0 0 24 24" fill="none" width="22" height="22">
      <circle cx="12" cy="7" r="4" stroke="#4e90a8" strokeWidth="2" />
      <path d="M4 21c0-4.418 3.582-8 8-8s8 3.582 8 8" stroke="#4e90a8" strokeWidth="2" strokeLinecap="round" />
    </svg>
  );
}
function IconMMI() {
  return (
    <svg viewBox="0 0 24 24" width="22" height="22" fill="none">
      <polygon points="12,3 22,20 2,20" stroke="#00a89d" strokeWidth="2" strokeLinejoin="round" />
    </svg>
  );
}
function IconComputer() {
  return (
    <svg viewBox="0 0 24 24" width="22" height="22" fill="none">
      <rect x="2" y="3" width="20" height="14" rx="2" stroke="#3d5a6e" strokeWidth="2" />
      <path d="M8 21h8M12 17v4" stroke="#3d5a6e" strokeWidth="2" strokeLinecap="round" />
    </svg>
  );
}
function IconChimie() {
  return (
    <svg viewBox="0 0 24 24" width="22" height="22" fill="none">
      <path d="M9 3v8L4 19a2 2 0 001.9 2.6h12.2A2 2 0 0020 19l-5-8V3" stroke="#00a89d" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
      <path d="M9 3h6" stroke="#00a89d" strokeWidth="2" strokeLinecap="round" />
      <circle cx="9" cy="15" r="1" fill="#00a89d" />
      <circle cx="14" cy="17" r="1" fill="#00a89d" />
    </svg>
  );
}

function BarChart() {
  const W = 320, H = 180, padX = 40, padY = 20, padBottom = 30;
  const maxVal = 3;
  const barW = 52;
  const chartH = H - padY - padBottom;
  const slots = BAR_DATA.map((d, i) => {
    const slotW = (W - padX * 2) / BAR_DATA.length;
    const x = padX + slotW * i + (slotW - barW) / 2;
    const barH = (d.count / maxVal) * chartH;
    const y = padY + chartH - barH;
    return { ...d, x, y, barH, midX: padX + slotW * i + slotW / 2 };
  });
  const gridLines = [0, 0.75, 1.5, 2.25, 3];

  return (
    <svg viewBox={`0 0 ${W} ${H}`} width="100%">
      {gridLines.map((v) => {
        const y = padY + chartH - (v / maxVal) * chartH;
        return (
          <g key={v}>
            <line x1={padX} x2={W - 20} y1={y} y2={y} stroke="#e5e7eb" strokeWidth="1" />
            <text x={padX - 6} y={y + 4} textAnchor="end" fontSize="9" fill="#9ca3af">{v}</text>
          </g>
        );
      })}
      {slots.map((s) => (
        <g key={s.dept}>
          <rect x={s.x} y={s.y} width={barW} height={s.barH} fill={s.color} rx="3" />
          <text x={s.midX} y={H - 8} textAnchor="middle" fontSize="10" fill="#6b7280">{s.dept}</text>
        </g>
      ))}
    </svg>
  );
}

function PieChart() {
  const cx = 85, cy = 85, r = 65;
  let cumulative = 0;
  const slices = PIE_DATA.map((d) => {
    const start = cumulative;
    cumulative += d.value;
    const startAngle = (start / 100) * 2 * Math.PI - Math.PI / 2;
    const endAngle = (cumulative / 100) * 2 * Math.PI - Math.PI / 2;
    const x1 = cx + r * Math.cos(startAngle);
    const y1 = cy + r * Math.sin(startAngle);
    const x2 = cx + r * Math.cos(endAngle);
    const y2 = cy + r * Math.sin(endAngle);
    const largeArc = d.value > 50 ? 1 : 0;
    return { ...d, path: `M ${cx} ${cy} L ${x1} ${y1} A ${r} ${r} 0 ${largeArc} 1 ${x2} ${y2} Z` };
  });

  return (
    <svg viewBox="0 0 170 170" width="170" height="170">
      {slices.map((s) => (
        <path key={s.label} d={s.path} fill={s.color} stroke="white" strokeWidth="2" />
      ))}
    </svg>
  );
}

const DEPT_OPTIONS = ["Tous", "MMI", "Informatique", "Chimie"];

const deptClass: Record<string, string> = {
  MMI: "dept-badge dept-mmi",
  Informatique: "dept-badge dept-informatique",
  Chimie: "dept-badge dept-chimie",
};

export default function AdminDashboard() {
  const [search, setSearch] = useState("");
  const [deptFilter, setDeptFilter] = useState("Tous");

  const filtered = VISITEURS.filter((v) => {
    const matchDept = deptFilter === "Tous" || v.dept === deptFilter;
    const q = search.toLowerCase();
    const matchSearch = !q || v.nom.toLowerCase().includes(q) || v.email.toLowerCase().includes(q) || v.etab.toLowerCase().includes(q);
    return matchDept && matchSearch;
  });

  return (
    <div>
      {/* Page header */}
      <Header
        title="Tableau de bord"
        subtitle="Gestion des journées d'immersion"
        actions={[
          { label: "Ajout journée Immersion", variant: "primary" },
          { label: "Ajout EDT JI", variant: "primary" },
          { label: "⚠ Alerte Présentateurs", variant: "warn" },
        ]}
      />

      {/* Stats */}
      <div className="stats-row">
        {STATS.map((s, i) => (
          <div key={i} className="stat-card">
            <div className="stat-icon">{s.icon}</div>
            <div className="stat-info">
              <span className="stat-value">{s.value}</span>
              <span className="stat-label">{s.label}</span>
            </div>
          </div>
        ))}
      </div>

      {/* Charts */}
      <div className="charts-row">
        <div className="chart-card">
          <h3 className="chart-title">Inscriptions par département</h3>
          <div className="chart-body">
            <BarChart />
          </div>
        </div>
        <div className="chart-card">
          <h3 className="chart-title">Répartition par établissement</h3>
          <div className="chart-body-center">
            <PieChart />
          </div>
        </div>
      </div>

      {/* Table */}
      <div className="table-card">
        <div className="table-header">
          <h3 className="table-title">Liste des visiteurs</h3>
          <div className="table-controls">
            <select className="filter-select" value={deptFilter} onChange={(e) => setDeptFilter(e.target.value)}>
              {DEPT_OPTIONS.map((o) => <option key={o}>{o}</option>)}
            </select>
            <div className="search-wrap">
              <svg className="search-icon" viewBox="0 0 20 20" fill="none" width="14" height="14">
                <circle cx="8.5" cy="8.5" r="5.5" stroke="#9ca3af" strokeWidth="1.5" />
                <path d="M13 13l3 3" stroke="#9ca3af" strokeWidth="1.5" strokeLinecap="round" />
              </svg>
              <input
                type="text"
                placeholder="Rechercher..."
                className="search-input"
                value={search}
                onChange={(e) => setSearch(e.target.value)}
              />
            </div>
          </div>
        </div>

        <table className="visitors-table">
          <thead>
            <tr>
              <th>Nom</th>
              <th>Email</th>
              <th>Département</th>
              <th>Établissement</th>
            </tr>
          </thead>
          <tbody>
            {filtered.map((v, i) => (
              <tr key={i}>
                <td className="td-nom">{v.nom}</td>
                <td className="td-email">{v.email}</td>
                <td><span className={deptClass[v.dept] ?? "dept-badge"}>{v.dept}</span></td>
                <td>{v.etab}</td>
              </tr>
            ))}
            {filtered.length === 0 && (
              <tr><td colSpan={4} className="table-empty">Aucun résultat</td></tr>
            )}
          </tbody>
        </table>
      </div>
    </div>
  );
}
