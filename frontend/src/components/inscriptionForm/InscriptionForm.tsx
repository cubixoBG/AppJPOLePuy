'use client';

import { useState } from 'react';
import { useRouter } from 'next/navigation';
import ButtonFull from '@components/buttonFull/ButtonFull';
import styles from '@/app/(main)/formulaireEmail/inscription/page.module.scss';

export default function InscriptionForm() {
    const router = useRouter();
    const [loading, setLoading] = useState(false);
    const [erreur, setErreur] = useState('');

    async function handleSubmit(e: React.FormEvent<HTMLFormElement>) {
        e.preventDefault();
        setErreur('');
        setLoading(true);

        const form = e.currentTarget;
        const data = {
            nom: (form.elements.namedItem('nom') as HTMLInputElement).value,
            prenom: (form.elements.namedItem('prenom') as HTMLInputElement).value,
            mail: (form.elements.namedItem('email') as HTMLInputElement).value,
            tel: (form.elements.namedItem('tel') as HTMLInputElement).value,
            etablissement: (form.elements.namedItem('etablissement') as HTMLInputElement).value,
        };

        try {
            const res = await fetch('/api/proxy/sendNotification', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data),
            });

            if (res.ok) {
                router.push('/formulaireEmail/inscription/validation');
            } else {
                setErreur("Une erreur est survenue. Veuillez réessayer.");
            }
        } catch {
            setErreur("Impossible de contacter le serveur.");
        } finally {
            setLoading(false);
        }
    }

    return (
        <form onSubmit={handleSubmit}>
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
                <label htmlFor="email">Email <span>*</span></label>
                <input type="email" name="email" id="email" placeholder="theo.martin@example.com" required />
            </div>
            <div className={styles.column}>
                <label htmlFor="tel">Téléphone <span>*</span></label>
                <input type="tel" name="tel" id="tel" placeholder="06 12 34 56 78" required />
            </div>
            <div className={styles.column}>
                <label htmlFor="etablissement">Établissement d'origine</label>
                <input type="text" name="etablissement" id="etablissement" placeholder="Lycée Polyvalent Saint Jacques de Compostelle" />
            </div>
            {erreur && <p style={{ color: 'red' }}>{erreur}</p>}
            <ButtonFull texte={loading ? 'Envoi...' : 'Valider'} lien="" />
            <span>* Obligatoire</span>
        </form>
    );
}
