import styles from "./page.module.scss";
import Footer from "@components/footer/Footer";
import Dashboard from "@components/dashboard_admin/dashboard_admin";
import ConnexionAdmin from "@components/connexionAdmin/ConnexionAdmin";

export default function AdminPage({ searchParams }: { searchParams: { auth?: string } }) {
    const authStatus = searchParams?.auth;
    const isAuthenticated = authStatus === "success";

    return (
        <main className={styles.admin} key={authStatus}>
            {/* {isAuthenticated ? ( */}
                <Dashboard />
            {/* ) : ( */}
                {/* <ConnexionAdmin /> */}
            {/* )} */}
            <Footer />
        </main>
    );
}