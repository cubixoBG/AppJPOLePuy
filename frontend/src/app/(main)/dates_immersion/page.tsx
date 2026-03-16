"use client"

import styles from "./page.module.scss";
import Footer from "@components/footer/Footer";
import { useRouter } from "next/navigation";
import { useState, useEffect } from "react";

export default function dates_immersion() {

    const router = useRouter();

    const [journees, setJournees] = useState([])

    useEffect( () => {
        if (sessionStorage.getItem("iduser") == undefined) {
            router.push("/formulaireEmail");
        }

        fetch()

    } , [])



    return (
        <main className={styles.main}>
            MMI
            <h1>Choisissez votre date</h1>
            <h2>Sélectionnez la date qui vous convient le mieux.</h2>
            <div className={styles.card_container}>
            <div className={styles.date_card}>
                <p>icône calendrier</p>
                <div className={styles.date_card_flex}>
                    <h3>Samedi 15 mars 2026</h3>
                    <h4>8 places restantes</h4>
                </div>
                <span className={styles.date_card_tag}>disponible</span>
            </div>
            <div className={styles.date_card}>
                <p>icône calendrier</p>
                <div className={styles.date_card_flex}>
                    <h3>Samedi 15 mars 2026</h3>
                    <h4>8 places restantes</h4>
                </div>
                <span className={styles.date_card_tag}>disponible</span>
            </div>
            <div className={styles.date_card}>
                <p>icône calendrier</p>
                <div className={styles.date_card_flex}>
                    <h3>Samedi 15 mars 2026</h3>
                    <h4>8 places restantes</h4>
                </div>
                <span className={styles.date_card_tag}>disponible</span>
            </div>

            </div>
            <Footer />
        </main>
    );
}