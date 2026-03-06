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
                <h2>Connexion</h2>
                <form onSubmit={checkEmail}>
                    <input 
                        type="email" 
                        value={email}
                        onChange={(e) => setEmail(e.target.value)}
                        placeholder="votre.email@exemple.fr" 
                        required 
                    />
                    <button type="submit" disabled={loading}>
                        {loading ? 'Vérification...' : 'Continuer ➔'}
                    </button>
                </form>
            </div>
        </section>
    );
}