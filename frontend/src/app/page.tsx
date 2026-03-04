import Image from "next/image";
import styles from "./page.module.scss";
import ButtonFull from "@components/buttonFull/ButtonFull";
import ButtonTransparent from "@components/buttonTransparent/ButtonTransparent";
import SectionImmersion from "@components/immersionSection/ImmersionSection";
import Footer from "@components/footer/Footer"

export default function Home() {

  return (
    <main className={styles.accueil}>
      <section className={styles.accueil_Top}>
        <div className={styles.accueil_Top_text}>
          <img src="/star.svg" alt="" />
          <h1>Bienvenue à l'IUT
            <br /><span>Site  du Puy-en-Velay</span></h1>
          <p>Nous sommes ravis de vous accueillir ! Inscrivez-vous en quelques
            clics pour profiter pleinement de votre visite.</p>
          <ButtonFull texte="Commencer ➔" lien="#accueil_Bottom" />
        </div>
      </section>
      <section className={styles.accueil_Bottom} id="accueil_Bottom">
        <SectionImmersion />

        <section className={styles.choixDep}>
          <div className={styles.choixDep_Container}>
            <h2>Choisissez un département</h2>
            <p>Sélectionnez le département qui vous intéresse le plus pour commencer votre inscription.</p>
            <div>
              <a href="#"><article>
                <img src="/paletteCouleur.webp" alt="icon MMI" />
                <h3>MMI</h3>
                <p>Métiers du Multimédia
                  et de l'Internet</p>
              </article></a>
              <a href="#"><article>
                <img src="/ordinateur.webp" alt="icon Info" />
                <h3>Informatique</h3>
                <p>Développement,
                  réseaux et systèmes</p>
              </article></a>
              <a href="#"><article>
                <img src="chimie.webp" alt="icon Chimie" />
                <h3>Chimie</h3>
                <p>Chimie analytique et de
                  synthèse</p>
              </article></a>
            </div>
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
              <ButtonTransparent texte="Réserver une date ➔" lien="" />
            </div>
          </div>
        </section>
        <Footer />
      </section>
    </main>
  );
}