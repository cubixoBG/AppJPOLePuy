'use client'
import { useState } from "react";
import styles from "./page.module.scss";

interface Visiteur {
    id: number;
    nom: string;
    prenom: string;
    mail: string;
    departement: string;
    etablissement: string;
}

interface Props {
    visiteurs: Visiteur[];
}

export default function VisiteursTable({ visiteurs }: Props) {
    const [filtre, setFiltre] = useState("Tous");

    const departements = ["Tous", ...Array.from(new Set(visiteurs.map((v) => v.departement)))];

    const filtres = filtre === "Tous"
        ? visiteurs
        : visiteurs.filter((v) => v.departement === filtre);

    return (
        <section className={styles.dashboard_visiteurs}>
            <div className={styles.header}>
                <h3>Liste des visiteurs</h3>
                <select
                    className={styles.select}
                    value={filtre}
                    onChange={(e) => setFiltre(e.target.value)}
                >
                    {departements.map((dep) => (
                        <option key={dep} value={dep}>{dep}</option>
                    ))}
                </select>
            </div>

            <table className={styles.table}>
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Département</th>
                        <th>Établissement</th>
                    </tr>
                </thead>
                <tbody>
                    {filtres.map((v) => (
                        <tr key={v.id}>
                            <td>{v.nom} {v.prenom}</td>
                            <td>{v.mail}</td>
                            <td>
                                <span className={`${styles.badge} ${styles[v.departement?.toLowerCase()]}`}>
                                    {v.departement}
                                </span>
                            </td>
                            <td>{v.etablissement}</td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </section>
    );
}