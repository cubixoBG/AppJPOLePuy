"use client"

import styles from "./page.module.scss";
import ButtonFull from "@components/buttonFull/ButtonFull";
import Footer from "@components/footer/Footer";
import { useState, useEffect } from "react";
import { useRouter } from "next/navigation";

export default function formulaireEmail() {

    const [email, setEmail] = useState("");
    const [userList, setUserList] = useState([])
    const router = useRouter();


    useEffect(() => {
        fetch('/api/proxy/getUsers')
            .then(res => res.json())
            .then(data => {
                setUserList(data.member);
            })
            .catch(err => console.error("Erreur fetch :", err));
    }, []);

    function handleSubmit(e: React.FormEvent<HTMLFormElement>) {

        e.preventDefault(); // ← empêche le rechargement

        const userFound = userList.find((user: any) => user.mail == email);

        if (userFound) {
            console.log("Utilisateur trouvé :", userFound);

            sessionStorage.setItem("iduser", userFound["@id"]);

            router.push("/dates_immersion");
        } else {
            console.log("Email introuvable");
        }

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
                            <input onChange={(e) => setEmail(e.target.value)} value={email} type="email" name="email" id="email" placeholder="votre.email@exemple.fr" required />
                        </div>
                        <button type="submit"> Continuer </button>
                    </form>
                </div>
            </section>
            <Footer />
        </main>
    );
}