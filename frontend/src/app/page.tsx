import Image from "next/image";
import styles from "./page.module.scss";
import ButtonFull from "@components/buttonFull/ButtonFull";

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
            <ButtonFull texte="Commencer ➔" lien="#accueil_ChoixDep" />
        </div>
      </section>
      <section className={styles.accueil_ChoixDep} id="accueil_ChoixDep">

      </section>
    </main>
  );
}