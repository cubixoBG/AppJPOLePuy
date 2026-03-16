import styles from "./page.module.scss";
import ButtonFull from "@components/buttonFull/ButtonFull";
import Footer from "@components/footer/Footer";

export default function identification() {

    return (
        <main>
            <section className={styles.formulaire}>
                <div className={styles.formulaire_container}>
                    <div className={styles.formulaire_container_header}>
                        <h2>Inscription</h2>
                        <p>Remplissez vos informations pour vous inscrire</p>
                    </div>
                    <form action="#">
                        <div className={styles.row}>
                            <div className={styles.column}>
                                <label htmlFor="nom">Nom <span>*</span></label>
                                <input type="text" name="nom" id="nom" placeholder="Martin" required />
                            </div>
                            <div className={styles.column}>
                                <label htmlFor="prenom">Prénom <span>*</span></label>
                                <input type="text" name="prenom" id="prenom" placeholder="Théo" required />
                            </div>
                        </div>
                        <div className={styles.column}>
                            <label htmlFor="email">Email</label>
                            <input type="email" name="email" id="email" value="#" required />
                        </div>
                        <div className={styles.column}>
                            <label htmlFor="tel">Téléphone <span>*</span></label>
                            <input type="tel" name="tel" id="tel" placeholder="06 12 34 56 78" required />
                        </div>
                        <div className={styles.column}>
                            <label htmlFor="etablissement">Établissement d'origine</label>
                            <input type="text" name="etablissement" id="etablissement" placeholder="Lycée Polyvalent Saint Jacques de Compostelle" />
                        </div>
                        <ButtonFull texte="Valider" lien="" />
                        <span>* Obligatoire</span>
                    </form>
                </div>
            </section>
            <Footer />
        </main>
    );
}