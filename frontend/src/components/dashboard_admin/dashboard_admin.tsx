"use client"
import ButtonFull from "@components/buttonFull/ButtonFull";
import styles from "./styles.module.scss";

export default function DashboardAdmin() {
    return (
        <main className={styles.dashboard}>
            <section className={styles.dashboard_header}>
                <div className={styles.dashboard_header_title}>
                    <h2>Tableau de Bord</h2>
                    <p>Gestion des journées d'immersion</p>
                </div>
                <div className={styles.dashbpard_header_buttons}>
                    <ButtonFull texte="Ajout journée d'immersion" lien="" />
                    <ButtonFull texte="Ajout EDT JI" lien="" />
                    <ButtonFull texte="Alerte Présentateur" lien="" />
                </div>
            </section>
            
            <section className={styles.dashboard_data}>
                <article className={styles.dashboard_data_visiteurs}>
                    <img src="#" alt="icon visiteurs" />
                    <div>
                        <h3>#</h3>
                        <p>Visiteurs totaux</p>
                    </div>
                </article>
                <article className={styles.dashboard_data_mmi}>
                    <img src="#" alt="icon mmi" />
                    <div>
                        <h3>#</h3>
                        <p>MMI</p>
                    </div>
                </article>
                <article className={styles.dashboard_data_info}>
                    <img src="#" alt="icon informatique" />
                    <div>
                        <h3>#</h3>
                        <p>Informatique</p>
                    </div>
                </article>
                <article className={styles.dashboard_data_chimie}>
                    <img src="#" alt="icon chimie" />
                    <div>
                        <h3>#</h3>
                        <p>Chimie</p>
                    </div>
                </article>
            </section>

            <section className={styles.dashboard_stats}>
                <article className={styles.dashboard_stats_dep}></article>
                <article className={styles.dashboard_stats_etab}></article>
            </section>

            <section className={styles.dashboard_visiteurs}></section>
        </main>
    );
}