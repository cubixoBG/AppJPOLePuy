import styles from "./page.module.scss";
import Footer from "@components/footer/Footer";
import ButtonFull from "@components/buttonFull/ButtonFull";
import ButtonTransparent from "@components/buttonTransparent/ButtonTransparent";
import SectionImmersion from "@components/immersionSection/ImmersionSection";

export default function validation() {

    return (
        <main className={styles.main}>
            <section className={styles.formulaire}>
                <div className={styles.formulaire_container}>
                    <div className={styles.formulaire_container_header}>
                        <img src="/letter.webp" alt="icon" />
                        <h2>Bienvenue, (nom) !</h2>
                        <p>Que souhaitez-vous faire ?</p>
                    </div>
                    <form action="#">
                        <ButtonFull texte="Réserver une journée d'immersion maintenant" lien="#" />
                        <ButtonTransparent texte="Je réserverais plus tard" lien="/" />
                    </form>
                    <p className={styles.textInfo}>Vous pourrez réserver une date à tout moment depuis la page d'accueil.</p>
                </div>
            </section>
            <SectionImmersion />
            <Footer />
        </main>
    );
}