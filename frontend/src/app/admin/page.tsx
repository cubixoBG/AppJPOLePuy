import styles from "./dashboard.module.css";

const MOCK_STATS = {
  visiteurs: 47,
  ambassadeurs: 12,
  presents: 38,
  immersions: 6,
};

const MOCK_VISITEURS = [
  { id: "1", nom: "Martin", prenom: "Lucas", etablissement: "Lycée Blaise Pascal", departement: "MMI", heure: "09:15" },
  { id: "2", nom: "Dupuis", prenom: "Camille", etablissement: "Lycée La Fayette", departement: "INFO", heure: "09:30" },
  { id: "3", nom: "Lebrun", prenom: "Théo", etablissement: "Lycée Ambroise Brugière", departement: "GEA", heure: "10:00" },
  { id: "4", nom: "Fontaine", prenom: "Jade", etablissement: "Lycée Virlogeux", departement: "MMI", heure: "10:15" },
];

const MOCK_AMBASSADEURS = [
  { id: "1", nom: "Arnaud", prenom: "Sophie", disponible: true,  salle: "B102" },
  { id: "2", nom: "Renard", prenom: "Mathieu", disponible: true,  salle: "Atrium" },
  { id: "3", nom: "Girard", prenom: "Clara", disponible: false, salle: "—" },
  { id: "4", nom: "Morel",  prenom: "Baptiste", disponible: true,  salle: "C201" },
];

export default function AdminDashboard() {
  return (
    <div className={styles.dashboard}>
      <div className={styles.pageHeader}>
        <h1 className={styles.pageTitle}>Tableau de bord</h1>
        <p className={styles.pageDate}>Samedi 14 mars 2026 — JPO IUT Clermont-Ferrand</p>
      </div>

      {/* Stats */}
      <div className={styles.statsGrid}>
        <StatCard value={MOCK_STATS.visiteurs}   label="Visiteurs inscrits"     color="#00a89d" icon="👤" />
        <StatCard value={MOCK_STATS.presents}    label="Personnes présentes"    color="#6366f1" icon="✅" />
        <StatCard value={MOCK_STATS.ambassadeurs} label="Ambassadeurs actifs"   color="#f59e0b" icon="🎓" />
        <StatCard value={MOCK_STATS.immersions}  label="Journées d'immersion"   color="#ec4899" icon="📅" />
      </div>

      <div className={styles.columns}>
        {/* Visiteurs */}
        <section className={styles.section}>
          <div className={styles.sectionHeader}>
            <h2 className={styles.sectionTitle}>Visiteurs</h2>
            <span className={styles.count}>{MOCK_VISITEURS.length}</span>
          </div>
          <div className={styles.tableWrap}>
            <table className={styles.table}>
              <thead>
                <tr>
                  <th>Nom</th>
                  <th>Établissement</th>
                  <th>Dept.</th>
                  <th>Arrivée</th>
                </tr>
              </thead>
              <tbody>
                {MOCK_VISITEURS.map((v) => (
                  <tr key={v.id}>
                    <td><strong>{v.prenom} {v.nom}</strong></td>
                    <td>{v.etablissement}</td>
                    <td><span className={styles.deptBadge}>{v.departement}</span></td>
                    <td>{v.heure}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </section>

        {/* Ambassadeurs */}
        <section className={styles.section}>
          <div className={styles.sectionHeader}>
            <h2 className={styles.sectionTitle}>Ambassadeurs</h2>
            <span className={styles.count}>{MOCK_AMBASSADEURS.length}</span>
          </div>
          <div className={styles.ambassadeurList}>
            {MOCK_AMBASSADEURS.map((a) => (
              <div key={a.id} className={styles.ambassadeurCard}>
                <div className={styles.avatar}>
                  {a.prenom[0]}{a.nom[0]}
                </div>
                <div className={styles.ambassadeurInfo}>
                  <span className={styles.ambassadeurName}>{a.prenom} {a.nom}</span>
                  {a.disponible && <span className={styles.salle}>Salle {a.salle}</span>}
                </div>
                <span className={a.disponible ? styles.badgeOk : styles.badgeMuted}>
                  {a.disponible ? "Disponible" : "Indisponible"}
                </span>
              </div>
            ))}
          </div>
        </section>
      </div>
    </div>
  );
}

function StatCard({ value, label, color, icon }: { value: number; label: string; color: string; icon: string }) {
  return (
    <div className={styles.statCard} style={{ "--accent": color } as React.CSSProperties}>
      <div className={styles.statIcon}>{icon}</div>
      <div className={styles.statValue}>{value}</div>
      <div className={styles.statLabel}>{label}</div>
    </div>
  );
}
