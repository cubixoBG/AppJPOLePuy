import styles from "./page.module.scss";
import ButtonFull from "@components/buttonFull/ButtonFull";
import ButtonTransparent from "@components/buttonTransparent/ButtonTransparent";
import SectionImmersion from "@components/immersionSection/ImmersionSection";
import Footer from "@components/footer/Footer"
import DepartementCards from "@components/departementCards/DepartementCards";

export default async function Home() {
  return (
    <main className={styles.accueil}>
      <section className={styles.accueil_Top}>
        <div className={styles.accueil_Top_text}>
          <img src="/star.svg" alt="" />
          <h1>Bienvenue à l'IUT
            <br /><span>Site  du Puy-en-Velay</span></h1>
          <p>Nous sommes ravis de vous accueillir ! Inscrivez-vous en quelques
            clics pour profiter pleinement de votre visite.</p>
          <a href="#accueil_Bottom" className={styles.boutonAccueil}>Commencer ➔</a>
        </div>
      </section>
      <section className={styles.accueil_Bottom} id="accueil_Bottom">
        <SectionImmersion />
        <section className={styles.choixDep}>
          <div className={styles.choixDep_Container}>
            <h2>Choisissez un département</h2>
            <p>Sélectionnez le département qui vous intéresse le plus pour commencer votre inscription.</p>
            <DepartementCards/>
          </div>
        </section>

        <section className={styles.datesImmersion}>
          <div className={styles.datesImmersion_container}>
            <div>
              <h3>Déjà inscrit ?</h3>
              <p>Réservez votre journée d'immersion pour vivre une journée complète au
                sein de l'IUT (cours, visite, échanges).</p>
            </div>
            <div className={styles.datesImmersion_container_right}>
              <ButtonTransparent texte="Réserver une date ➔" lien="./formulaireEmail" />
            </div>
          </div>
        </section>
        <Footer />
      </section>
    </main>
  );
}