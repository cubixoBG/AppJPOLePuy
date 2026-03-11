"use client"
import React, { useState, useEffect} from 'react';
import styles from "./styles.module.scss";

export default function ConnexionForm({ onSuccess }) {
    const [email, setEmail] = useState('');
    const [loading, setLoading] = useState(false);

    const [response, setResponse] = useState([]);

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

    function searchEmail() {
        useEffect(() => {
            fetch('/api/proxy/getUserByEmail') // <= configurer les appels api dans le proxy
                .then(res => res.json())
                .then(data => {
                    setResponse(data);
                    
                })
                .catch(err => console.error("Erreur fetch :", err));},
        []);
    }

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