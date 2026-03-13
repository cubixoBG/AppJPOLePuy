"use client"

import styles from "./page.module.scss";
import ButtonFull from "@components/buttonFull/ButtonFull";
import Footer from "@components/footer/Footer";
import { useState, useEffect } from "react";

export default function formulaireEmail() {

    const [email, setEmail] = useState([]);

    const handleSubmit = async (e: any) => {
        e.preventDefault();

        // email

        fetch('/api/proxy/getUsers')
            .then(res => res.json())
            .then(data => {
                setEmail(data.member);
            })
            .catch(err => console.error("Erreur fetch :", err));
            setEmail("");
        }
    };

    return (
        <main>
            <section className={styles.formulaire}>
                <div className={styles.formulaire_container}>
                    <div className={styles.formulaire_container_header}>
                        <img src="/letter.webp" alt="icon" />
                        <h2>Identification</h2>
                        <p>Entrez votre adresse email pour continuer</p>
                    </div>
                    <form action="#">
                        <div>
                            <label htmlFor="email">Adresse email</label>
                            <input type="email" name="email" id="email" placeholder="votre.email@exemple.fr" required />
                        </div>
                        <ButtonFull texte="Continuer ➔" lien="" />
                    </form>
                </div>
            </section>
            <Footer />
        </main>
    );
}