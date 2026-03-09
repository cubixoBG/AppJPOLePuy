import styles from "./page.module.scss";
import ButtonFull from "@components/buttonFull/ButtonFull";
import Footer from "@components/footer/Footer";

export default function admin() {

    return (
        <main className={styles.admin}>
            <div className={styles.admin_container}>
                <section className={styles.admin_container_header}>
                    <img src="/cadenas.webp" alt="icon admin" />
                    <h2>Espace Admin</h2>
                </section>
                <section className={styles.admin_container_connexion}>
                    <label htmlFor="mdp">Mot de passe</label>
                    <input type="text" name="mdp" id="mdp" placeholder="Entrez le mot de passe admin" required />
                    <ButtonFull texte="Se connecter" lien="" />
                </section>
            </div>
        </main>
    );
}