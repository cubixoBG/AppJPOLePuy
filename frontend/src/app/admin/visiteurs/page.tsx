import styles from "./visiteurs.module.css";
const MOCK = [
  { id:"1", nom:"Martin",   prenom:"Lucas",   email:"lucas@lycee-pascal.fr",  etab:"Lycée Blaise Pascal",     dept:"MMI",  heure:"09:15" },
  { id:"2", nom:"Dupuis",   prenom:"Camille", email:"camille@famille.fr",      etab:"Lycée La Fayette",        dept:"INFO", heure:"09:30" },
  { id:"3", nom:"Lebrun",   prenom:"Théo",    email:"theo.lebrun@mail.fr",     etab:"Lycée A. Brugière",       dept:"GEA",  heure:"10:00" },
  { id:"4", nom:"Fontaine", prenom:"Jade",    email:"jade.f@outlook.fr",       etab:"Lycée Virlogeux",         dept:"MMI",  heure:"10:15" },
  { id:"5", nom:"Petit",    prenom:"Hugo",    email:"hugo.petit@mail.fr",      etab:"Lycée Beau Soleil",       dept:"RT",   heure:"10:30" },
  { id:"6", nom:"Bernard",  prenom:"Inès",    email:"ines.bernard@gmail.com",  etab:"Lycée du Brivadois",      dept:"MMI",  heure:"11:00" },
];
export default function VistiteursPage() {
  return (
    <div className={styles.page}>
      <div className={styles.header}>
        <h1 className={styles.title}>Visiteurs inscrits</h1>
        <p className={styles.sub}>{MOCK.length} personnes enregistrées</p>
      </div>
      <div className={styles.tableCard}>
        <table className={styles.table}>
          <thead><tr><th>Visiteur</th><th>Email</th><th>Établissement</th><th>Dept.</th><th>Arrivée</th></tr></thead>
          <tbody>
            {MOCK.map((v) => (
              <tr key={v.id}>
                <td><div className={styles.nameCell}><div className={styles.avatar}>{v.prenom[0]}{v.nom[0]}</div><span>{v.prenom} {v.nom}</span></div></td>
                <td className={styles.email}>{v.email}</td>
                <td>{v.etab}</td>
                <td><span className={styles.dept}>{v.dept}</span></td>
                <td>{v.heure}</td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}
