"use client"
import { useState } from "react";
import ConnexionForm from "@components/connexionForm/ConnexionForm";
import styles from "./styles.module.scss";

export default function ConnexionManager() {
    const [isIdentified, setIsIdentified] = useState(false);

    if (isIdentified) {
        return (
            <section className={styles.choixDateImmersion}>
                <div className={styles.choixDateImmersion_header}>
                    <span>MMI</span>
                    <h2>Choisissez votre date</h2>
                    <p>Sélectionnez la date qui vous convient le mieux.</p>
                </div>
                <div className={styles.choixDateImmersion_dates}>
                    
                </div>
            </section>
        );
    }

    return <ConnexionForm onSuccess={() => setIsIdentified(true)} />;
}