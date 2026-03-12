"use client"
import React, { useState } from 'react';
import styles from "./styles.module.scss";

export default function ConnexionForm({ onSuccess }) {
    const [email, setEmail] = useState('');
    const [loading, setLoading] = useState(false);

    const checkEmail = async (e) => {
        e.preventDefault();
        setLoading(true);

        setTimeout(() => {
            const emailExists = true;
            if (emailExists) {
                onSuccess();
            } else {
                alert("Email inconnu");
            }
            setLoading(false);
        }, 1000);
    };

    return (
        <section className={styles.formulaire}>
            <div className={styles.formulaire_container}>
                <div className={styles.formulaire_container_header}>
                    <img src="/letter.webp" alt="icon" />
                    <h2>Connexion</h2>
                    <p>Entrez votre adresse email pour vous connecter</p>
                </div>
                <form onSubmit={checkEmail}>
                    <input
                        type="email"
                        value={email}
                        onChange={(e) => setEmail(e.target.value)}
                        placeholder="votre.email@exemple.fr"
                        required
                    />
                    <button type="submit" disabled={loading}>
                        <p>{loading ? 'Vérification...' : 'Continuer ➔'}</p>
                    </button>
                </form>
            </div>
        </section>
    );
}