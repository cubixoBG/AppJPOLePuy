import styles from "./page.module.scss";
import Footer from "@components/footer/Footer";
import InscriptionForm from "@components/inscriptionForm/InscriptionForm";

export default function identification() {

    return (
        <main>
            <section className={styles.formulaire}>
                <div className={styles.formulaire_container}>
                    <div className={styles.formulaire_container_header}>
                        <h2>Inscription</h2>
                        <p>Remplissez vos informations pour vous inscrire</p>
                    </div>
                    <InscriptionForm />
                </div>
            </section>
            <Footer />
        </main>
    );
}