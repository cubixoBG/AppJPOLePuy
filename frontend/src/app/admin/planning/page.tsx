import styles from "./planning.module.css";
const JOURNEES = [
  { id:"1", date:"Lundi 9 mars 2026",     dept:"MMI",          places:8, inscrits:6, edt:"MMI-01" },
  { id:"2", date:"Mercredi 11 mars 2026", dept:"Informatique", places:8, inscrits:8, edt:"INFO-01" },
  { id:"3", date:"Vendredi 13 mars 2026", dept:"GEA",          places:6, inscrits:3, edt:"GEA-01" },
  { id:"4", date:"Lundi 16 mars 2026",    dept:"RT",           places:6, inscrits:5, edt:"RT-01" },
];
export default function PlanningPage() {
  return (
    <div className={styles.page}>
      <h1 className={styles.title}>Planning des immersions</h1>
      <p className={styles.sub}>Gestion des journées d'immersion par département</p>
      <div className={styles.cards}>
        {JOURNEES.map((j) => {
          const pct = Math.round((j.inscrits / j.places) * 100);
          const full = j.inscrits >= j.places;
          return (
            <div key={j.id} className={styles.card}>
              <div className={styles.cardTop}>
                <span className={styles.dept}>{j.dept}</span>
                <span className={full ? styles.full : styles.open}>{full ? "Complet" : "Places disponibles"}</span>
              </div>
              <p className={styles.cardDate}>{j.date}</p>
              <div className={styles.progress}>
                <div className={styles.progressBar} style={{ width: `${pct}%` }} />
              </div>
              <p className={styles.progressLabel}>{j.inscrits}/{j.places} inscrits</p>
              <p className={styles.edt}>EDT : {j.edt}</p>
            </div>
          );
        })}
      </div>
    </div>
  );
}
