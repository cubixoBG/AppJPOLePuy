'use client';

import { useRouter } from 'next/navigation';
import styles from "./page.module.scss";
import Footer from "@components/footer/Footer";

export default function FormulaireEmail() {
    const router = useRouter();

    function handleSubmit(e: React.FormEvent<HTMLFormElement>) {
        e.preventDefault();
        const email = (e.currentTarget.elements.namedItem('email') as HTMLInputElement).value;
        router.push(`/formulaireEmail/inscription?email=${encodeURIComponent(email)}`);
    }

    return (
        <main>
            <section className={styles.formulaire}>
                <div className={styles.formulaire_container}>
                    <div className={styles.formulaire_container_header}>
                        <img src="/letter.webp" alt="icon" />
                        <h2>Identification</h2>
                        <p>Entrez votre adresse email pour continuer</p>
                    </div>
                    <form onSubmit={handleSubmit}>
                        <div>
                            <label htmlFor="email">Adresse email</label>
                            <input type="email" name="email" id="email" placeholder="votre.email@exemple.fr" required />
                        </div>
                        <button type="submit">Continuer ➔</button>
                    </form>
                </div>
            </section>
            <Footer />
        </main>
    );
}