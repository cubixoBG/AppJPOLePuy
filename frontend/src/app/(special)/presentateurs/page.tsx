'use client';

import styles from "./page.module.scss";
import ButtonFull from "@components/buttonFull/ButtonFull";
import Footer from "@components/footer/Footer";
import { useEffect, useState } from 'react';

export default function Page() {

    const [deps, setDeps] = useState([]);
    const [selectedDep, setSelectedDep] = useState(null);
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

    console.log(deps);
    console.log(indices);
    console.log(selectedDep);
    console.log(contacts);

    return (
        <main className={styles.presentateur}>
            <section className={styles.presentateur_header}>
                <h2>Espace Présentateur</h2>
                <p>Outils et informations pour accompagner les visiteurs</p>
            </section>
            <section className={styles.presenteur_memo}>
                <div className={styles.presentateur_memo_filtre}>

                </div>
                <div className={styles.presentateur_memo_container}>
                    <article>
                        <h3>Département</h3>
                        <select onChange={(e) => {
                            console.log(e.target.value)
                            const dep = deps.find((d: any) => e.target.value == d.id);
                            setSelectedDep(dep["@id"]);
                            }}>
                            <option selected disabled>Choisissez un département</option>
                            {deps.map((d : any) => <option key={d.id} value={d.id}>{d.logo} {d.nom} {d.description} {d.responsable}</option>)}
                        </select>
                        <div>
                            {indices
                                .filter((i: any) => i.departement == selectedDep) // filtre par le département sélectionné
                                .map((i: any) => (
                                <div key={i.id}>
                                    {i.texte}
                                </div>
                            ))}                        
                        </div>
                    </article>
                </div>
            </section>
            <section className={styles.presentateur_boutons}>
                
            </section>
            <section className={styles.presentateur_contacts}>
                <div className={styles.presentateur_contacts_container}>
                    <article>
                        {contacts
                            .filter((c: any) => c.departement == selectedDep)
                            .map((c: any) => (
                            <div key={c.id}>
                                {c.nom} {c.prenom} {c.mail} {c.type} {c.domaine}
                            </div>
                        ))}
                    </article>
                </div>
            </section>
            <Footer />
        </main>
    );
}