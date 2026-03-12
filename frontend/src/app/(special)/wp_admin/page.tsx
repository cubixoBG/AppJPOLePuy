import styles from "./page.module.scss";
import Footer from "@components/footer/Footer";
import ButtonFull from "@components/buttonFull/ButtonFull";
import GraphiqueBarre from "@components/graphiqueBarre/BarChart"
import GraphiqueCercle from "@components/graphiqueCercle/CercleChart"
import VisiteursTable from "@components/visiteursTable/VisiteursTable"

export default async function AdminPage({ searchParams }: { searchParams: { auth?: string } }) {
    const authStatus = searchParams?.auth;
    const isAuthenticated = authStatus === "success";

    const response = await fetch("http://webserver:80/api/users", {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'x-api-key': process.env.API_KEY || "",
        },
        cache: 'no-store',
    });

    const data = await response.json();

    const users = data.member || [];

    const totalVisiteurs = users.filter((u: any) =>
        u.type !== "Admin" && u.type !== "Presentateur" && u.type !== "Presentateur"
    ).length;

    const mmiCount = users.filter((u: any) =>
        u.type !== "Admin" && u.type !== "Presentateur" && u.departement === "MMI"
    ).length;

    const infoCount = users.filter((u: any) =>
        u.type !== "Admin" && u.type !== "Presentateur" && u.departement === "Informatique"
    ).length;

    const chimieCount = users.filter((u: any) =>
        u.type !== "Admin" && u.type !== "Presentateur" && u.departement === "Chimie"
    ).length;

    const visiteurs = users.filter((u: any) =>
        u.type !== "Admin" && u.type !== "Presentateur"
    );

    return (
        <main className={styles.admin} key={authStatus}>
            <div className={styles.dashboard}>
                {/* HEADER */}
                <section className={styles.dashboard_header}>
                    <div className={styles.dashboard_header_title}>
                        <h2>Tableau de Bord</h2>
                        <p>Gestion des journées d'immersion</p>
                    </div>
                    <div className={styles.dashboard_header_buttons}>
                        <ButtonFull texte="Ajout journée d'immersion" lien="" color={2} />
                        <ButtonFull texte="Ajout EDT JI" lien="" color={2} />
                        <ButtonFull texte="Alerte Présentateur" lien="" />
                    </div>
                </section>

                {/* STATS RAPIDES (DATA) */}
                <section className={styles.dashboard_data}>
                    <article className={styles.dashboard_data_visiteurs}>
                        <img src="/utilisateur.webp" alt="icon visiteurs" />
                        <div>
                            <h3>{totalVisiteurs}</h3>
                            <p>Visiteurs totaux</p>
                        </div>
                    </article>
                    <article className={styles.dashboard_data_mmi}>
                        <img src="/paletteCouleur.webp" alt="icon mmi" />
                        <div>
                            <h3>{mmiCount}</h3>
                            <p>MMI</p>
                        </div>
                    </article>
                    <article className={styles.dashboard_data_info}>
                        <img src="/ordinateur.webp" alt="icon informatique" />
                        <div>
                            <h3>{infoCount}</h3>
                            <p>Informatique</p>
                        </div>
                    </article>
                    <article className={styles.dashboard_data_chimie}>
                        <img src="/chimie.webp" alt="icon chimie" />
                        <div>
                            <h3>{chimieCount}</h3>
                            <p>Chimie</p>
                        </div>
                    </article>
                </section>

                <section className={styles.dashboard_stats}>
                    <article className={styles.dashboard_stats_dep}>
                        <h3>Inscriptions par département</h3>
                        <div>
                            <GraphiqueBarre />
                        </div>
                    </article>
                    <article className={styles.dashboard_stats_etab}>
                        <h3>Répartitions par établissement</h3>
                        <div>
                            <GraphiqueCercle />
                        </div>
                    </article>
                </section>

                <VisiteursTable visiteurs={visiteurs} />
            </div>
            <Footer />
        </main>
    );
}