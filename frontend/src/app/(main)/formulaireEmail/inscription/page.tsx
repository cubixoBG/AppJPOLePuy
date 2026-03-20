import styles from "./page.module.scss";
import Footer from "@components/footer/Footer";
import InscriptionForm from "@components/inscriptionForm/InscriptionForm";

type Props = {
    searchParams: { email?: string } | Promise<{ email?: string }>;
};

export default async function identification({ searchParams }: Props) {
    const params = await Promise.resolve(searchParams);
    const email = params.email ?? '';

    return (
        <main>
            <section className={styles.formulaire}>
                <div className={styles.formulaire_container}>
                    <div className={styles.formulaire_container_header}>
                        <h2>Inscription</h2>
                        <p>Remplissez vos informations pour vous inscrire</p>
                    </div>
                    <InscriptionForm initialEmail={email} />
                </div>
            </section>
            <Footer />
        </main>
    );
}