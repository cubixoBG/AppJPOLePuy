"use client"

import styles from "./page.module.scss";
import Footer from "@components/footer/Footer";
import { useRouter } from "next/navigation";
import { useState, useEffect } from "react";

export default function dates_immersion() {

    const router = useRouter();

    const [journees, setJournees] = useState([]);
    const [selectedJournees, setSelectedJournees] = useState([]);

    const [deps, setDeps] = useState([]);
    const [selectedDep, setSelectedDep] = useState([]);

    useEffect( () => {
        if (!sessionStorage.getItem("iduser")) {
            router.push("/formulaireEmail");
        }

        fetch('/api/proxy/getDeps')
            .then(res => res.json())
            .then(data => {
                setDeps(data.member);
            })
        fetch('/api/proxy/getJournees')
            .then(res => res.json())
            .then(data => {
                setJournees(data.member);
            })
    }, [])

    useEffect( () => {
        const filtered = journees.filter(j => j.departement === selectedDep);
        setSelectedJournees(filtered);
    } , [selectedDep])

    function handleReserver(id_journee: any) {
        // sessionStorage.getitem("iduser");



        fetch("/api/proxy/getUsers", {
            method: "PATCH",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({
                id_journee: id_journee,
                id_user: sessionStorage.getItem("iduser")
            })
        });    
    }

    return (
        <main className={styles.main}>
            <select className={styles.presentateur_memo_select} onChange={(e) => {
                console.log(e.target.value)
                const dep = deps.find((d: any) => e.target.value == d.id);
                setSelectedDep(dep["@id"]);
                }}>
                <option selected disabled>Choisissez un département</option>
                {deps.map((d : any) => <option key={d.id} value={d.id}>{d.logo} {d.nom} {d.description} {d.responsable}</option>)}
            </select>
            <h1>Choisissez votre date</h1>
            <h2>Sélectionnez la date qui vous convient le mieux.</h2>
            <div className={styles.card_container}>
            {selectedJournees.map((j: any) => 
                <div className={styles.date_card}>
                    <p>icône calendrier</p>
                    <div className={styles.date_card_flex}>
                        <h3>{j.date}</h3>
                        <h4>{8 - j.id_user.length} places restantes</h4>
                    </div>
                    <button   onClick={() => handleReserver(j["@id"])} className={styles.date_card_button}> Réserver </button>
                    <span className={styles.date_card_tag}>   {8 - j.id_user.length <= 0 ? "indisponible" : "disponible"}</span>
                </div>
            )}
            </div>
            <Footer />
        </main>
    );
}