import styles from "./page.module.scss";
import ButtonFull from "@components/buttonFull/ButtonFull";
import Footer from "@components/footer/Footer";

export default function FormulaireEmail() {

    return (
        <main>
            <div className={styles.formEmail}>
                <section className={styles.formulaire}>
                    <div className={styles.formulaire_container}>
                        <div className={styles.formulaire_container_header}>
                            <img src="/letter.webp" alt="icon" />
                            <h2>Identification</h2>
                            <p>Entrez votre adresse email pour continuer</p>
                        </div>
                        <form action="#">
                            <label htmlFor="email">Adresse email</label>
                            <input type="email" name="email" id="email" placeholder="votre.email@exemple.fr" required />
                            <ButtonFull texte="Continuer ➔" lien="" />
                        </form>
                    </div>
                </section>
            </div>
            <Footer />
        </main>
    );
}