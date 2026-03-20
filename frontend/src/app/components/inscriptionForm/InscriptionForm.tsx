'use client';

import { useState } from 'react';
import { useRouter } from 'next/navigation';
import styles from './InscriptionForm.module.scss';

type Props = {
    initialEmail?: string;
};

export default function InscriptionForm({ initialEmail = '' }: Props) {
    const router = useRouter();
    const [formData, setFormData] = useState({
        nom: '',
        prenom: '',
        mail: initialEmail,
        tel: '',
        etablissement: '',
    });
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState('');

    function handleChange(e: React.ChangeEvent<HTMLInputElement>) {
        setFormData(prev => ({ ...prev, [e.target.name]: e.target.value }));
    }

    async function handleSubmit(e: React.FormEvent<HTMLFormElement>) {
        e.preventDefault();
        setLoading(true);
        setError('');

        try {
            const response = await fetch('/api/proxy/sendNotification', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData),
            });

            if (!response.ok) {
                throw new Error('Erreur serveur');
            }

            router.push('/formulaireEmail/inscription/validation');
        } catch (err) {
            setError('Une erreur est survenue. Veuillez réessayer.');
        } finally {
            setLoading(false);
        }
    }

    return (
        <form onSubmit={handleSubmit} className={styles.form}>
            <div className={styles.field}>
                <label htmlFor="nom">Nom <span>*</span></label>
                <input
                    type="text"
                    id="nom"
                    name="nom"
                    value={formData.nom}
                    onChange={handleChange}
                    required
                />
            </div>

            <div className={styles.field}>
                <label htmlFor="prenom">Prénom <span>*</span></label>
                <input
                    type="text"
                    id="prenom"
                    name="prenom"
                    value={formData.prenom}
                    onChange={handleChange}
                    required
                />
            </div>

            <div className={styles.field}>
                <label htmlFor="mail">Email <span>*</span></label>
                <input
                    type="email"
                    id="mail"
                    name="mail"
                    value={formData.mail}
                    onChange={handleChange}
                    required
                />
            </div>

            <div className={styles.field}>
                <label htmlFor="tel">Téléphone</label>
                <input
                    type="tel"
                    id="tel"
                    name="tel"
                    value={formData.tel}
                    onChange={handleChange}
                />
            </div>

            <div className={styles.field}>
                <label htmlFor="etablissement">Établissement d&apos;origine</label>
                <input
                    type="text"
                    id="etablissement"
                    name="etablissement"
                    value={formData.etablissement}
                    onChange={handleChange}
                />
            </div>

            {error && <p className={styles.error}>{error}</p>}

            <button type="submit" disabled={loading} className={styles.submit}>
                {loading ? 'Envoi en cours...' : 'Valider'}
            </button>

            <p className={styles.required}>* Obligatoire</p>
        </form>
    );
}
