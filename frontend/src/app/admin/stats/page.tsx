import styles from "./stats.module.css";
const DEPTS = [
  { nom: "MMI", count: 18, color: "#00a89d" },
  { nom: "Informatique", count: 12, color: "#6366f1" },
  { nom: "GEA", count: 9, color: "#f59e0b" },
  { nom: "RT", count: 5, color: "#ec4899" },
  { nom: "Génie Civil", count: 3, color: "#10b981" },
];
const MAX = 18;
export default function StatsPage() {
  return (
    <div className={styles.page}>
      <h1 className={styles.title}>Statistiques</h1>
      <p className={styles.sub}>Données de la JPO — 14 mars 2026</p>
      <div className={styles.grid}>
        <div className={styles.card}>
          <p className={styles.cardLabel}>Visiteurs par département</p>
          <div className={styles.bars}>
            {DEPTS.map((d) => (
              <div key={d.nom} className={styles.barRow}>
                <span className={styles.barLabel}>{d.nom}</span>
                <div className={styles.barTrack}>
                  <div className={styles.barFill} style={{ width: `${(d.count / MAX) * 100}%`, background: d.color }} />
                </div>
                <span className={styles.barCount}>{d.count}</span>
              </div>
            ))}
          </div>
        </div>
        <div className={styles.card}>
          <p className={styles.cardLabel}>Taux de présence</p>
          <div className={styles.donut}>
            <svg viewBox="0 0 120 120" width="120" height="120">
              <circle cx="60" cy="60" r="45" fill="none" stroke="#f3f4f6" strokeWidth="12"/>
              <circle cx="60" cy="60" r="45" fill="none" stroke="#00a89d" strokeWidth="12"
                strokeDasharray={`${(38/47)*2*Math.PI*45} ${2*Math.PI*45}`}
                strokeDashoffset={2*Math.PI*45*0.25}
                strokeLinecap="round"
                style={{ transform: "rotate(-90deg)", transformOrigin: "60px 60px" }}
              />
            </svg>
            <div className={styles.donutCenter}>
              <span className={styles.donutPct}>81%</span>
              <span className={styles.donutSub}>présents</span>
            </div>
          </div>
          <p className={styles.donutDetail}>38 présents sur 47 inscrits</p>
        </div>
      </div>
    </div>
  );
}
