"use client"
import { useState } from 'react';
import styles from "./styles.module.scss";
import ButtonFull from "@components/buttonFull/ButtonFull";

export default function Login_Admin() {
    const [password, setPassword] = useState("");

    const handleConnexion = () => {
        if (password === "LePuy2026") {
            window.location.href = "/wp_admin?auth=success";
        } else {
            alert("Mot de passe incorrect");
        }
    };

    return (
        <div className={styles.admin_container}>
            <section className={styles.admin_container_header}>
                <img src="/cadenas.webp" alt="icon admin" />
                <h2>Espace Admin</h2>
            </section>
            <section className={styles.admin_container_connexion}>
                <label htmlFor="mdp">Mot de passe</label>
                <input 
                    type="password" 
                    onChange={(e) => setPassword(e.target.value)}
                    placeholder="Entrez le mot de passe admin" 
                    onKeyDown={(e) => e.key === "Enter" && handleConnexion()}
                />
                <div onClick={handleConnexion} style={{ cursor: 'pointer' }}>
                    <ButtonFull texte="Se connecter" lien="" />
                </div>
            </section>
        </div>
    );
}