import styles from "./page.module.scss";

export default function ImmersionSection() {

    return (
        <section className={styles.immersion}>
            <div className={styles.immersion_texts}>
                <h2>Qu'est-ce qu'une journée d'immersion ?</h2>
                <p>Une journée d'immersion vous permet de découvrir le site du Puy-en-Velay de
                    l'intérieur : vous assistez à de vrais cours, visitez les locaux, échangez avec les étudiants et
                    les enseignants, et déjeunez au restaurant universitaire.</p>
            </div>
            <div className={styles.immersion_infos}>
                <article>
                    <img src="/calendrier.webp" alt="icon calendrier" />
                    <p>Plusieurs dates au choix</p>
                </article>
                <article>
                    <img src="/horloge.webp" alt="icon horloge" />
                    <p>De 9h à 16h</p>
                </article>
                <article>
                    <img src="/utilisateur.webp" alt="icon utilisateurs" />
                    <p>Places limitées</p>
                </article>
            </div>
        </section>
    );
}