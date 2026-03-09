import styles from "./page.module.scss";
import ButtonFull from "@components/buttonFull/ButtonFull";
import Footer from "@components/footer/Footer";

export default function presentateurs() {

    return (
        <main className={styles.presentateur}>
            <section className={styles.presentateur_header}>
                <h2>Espace Présentateur</h2>
                <p>Outils et informations pour accompagner les visiteurs</p>
            </section>
            <section className={styles.presenteur_memo}>
                <div className={styles.presentateur_memo_filtre}>

                </div>
                <div className={styles.presentateur_memo_container}>
                    <article>
                        <h3>Département</h3>
                        
                    </article>
                </div>
            </section>
            <section className={styles.presentateur_boutons}>
                
            </section>
            <section className={styles.presentateur_contacts}>
                <div className={styles.presentateur_contacts_container}>
                    <article>

                    </article>
                </div>
            </section>
            <Footer />
        </main>
    );
}