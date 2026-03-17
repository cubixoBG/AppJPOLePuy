'use client';

import styles from "./page.module.scss";
import ButtonFull from "@components/buttonFull/ButtonFull";
import Footer from "@components/footer/Footer";
import { useEffect, useState } from 'react';
import Link from "next/link";

export default function Page() {

    const [deps, setDeps] = useState([]);
    const [selectedDep, setSelectedDep] = useState("");
    const [indices, setIndices] = useState([]);
    const [contacts, setContacts] = useState([]);

    useEffect(() => {
        fetch('/api/proxy/getDeps')
            .then(res => res.json())
            .then(data => {
                setDeps(data.member);
            })
            .catch(err => console.error("Erreur fetch :", err));},
    []);

    useEffect(() => {
        fetch('/api/proxy/getIndices')
            .then(res => res.json())
            .then(data => {
                setIndices(data.member);
            })
            .catch(err => console.error("Erreur fetch :", err));},
    []);

    useEffect(() => {
        fetch('/api/proxy/getContacts')
            .then(res => res.json())
            .then(data => {
                setContacts(data.member);
            })
            .catch(err => console.error("Erreur fetch :", err));},
    []);

    return (
        <main className={styles.presentateur}>
            <section className={styles.presentateur_header}>
                <h2>Espace Présentateur</h2>
                <p>Outils et informations pour accompagner les visiteurs</p>
            </section>
            <section className={styles.presentateur_memo}>
                <div className={styles.presentateur_memo_filtre}>
                    <h3>Département</h3>
                    <select className={styles.presentateur_memo_select} onChange={(e) => {
                        const dep = deps.find((d: any) => e.target.value == d.id);
                        setSelectedDep(dep["@id"]);
                        }}>
                        <option selected disabled>Choisissez un département</option>
                        {deps.map((d : any) => <option key={d.id} value={d.id}>{d.logo} {d.nom} {d.description} {d.responsable}</option>)}
                    </select>
                </div>
                <div className={styles.presentateur_memo_container}>
                    <article>
                        <div className={styles.presentateur_memo_list}>
                            {indices
                                .filter((i: any) => i.departement == selectedDep) // filtre par le département sélectionné
                                .map((i: any) => (
                                <p className={styles.indice} key={i.id}>
                                    <img src="/openbook.svg" alt="book"/> {i.texte}
                                </p>
                            ))}                        
                        </div>
                    </article>
                </div>
            </section>
            <section className={styles.presentateur_boutons}>
                {/* TODO bouton pour questionnaire */}
                {/* TODO bouton pour statut */}
                <button className={styles.button} id="questionnaire">
                    <Link href="./questionnaire">Questionnaire de satisfaction</Link>
                </button>
                <button className={styles.button} id="state">
                    bouton pour statut à 3 états
                </button>
            </section>
            <section className={styles.presentateur_contacts}>
                <article className={styles.presentateur_contacts_container}>
                    {contacts
                        .filter((c: any) => c.departement == selectedDep)
                        .map((c: any) => (
                        <div className={styles.presentateur_contacts_card} key={c.id}>
                            <p className={styles.presentateur_contacts_name}>{c.nom} | {c.prenom} </p>
                            <p className={styles.presentateur_contacts_mail}>{c.mail}</p>
                            <p className={styles.presentateur_contacts_domaine}>{c.type} | {c.domaine}</p>
                        </div>
                    ))}
                </article>
            </section>
            <Footer />
        </main>
    );
}