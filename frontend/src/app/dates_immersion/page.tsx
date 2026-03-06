import styles from "./page.module.scss";
import Footer from "@components/footer/Footer";
import ConnexionManager from "@components/connexionManager/ConnexionManager";

export default function dates_immersion() {
    return (
        <main>
            <ConnexionManager />
            <Footer />
        </main>
    );
}