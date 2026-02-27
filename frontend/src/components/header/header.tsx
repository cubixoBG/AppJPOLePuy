import styles from './page.module.scss';

export default function Header({type = 1}) {
    let navContent;

    if (type === 1) {
        navContent = (
            <ul>
                <li><a href="#">Présentateur</a></li>
                <li><a href="#">Admin</a></li>
            </ul>
        );
    } else {
        navContent = (
            <ul>
                <li><a href="./app/page.tsx">Retour au site</a></li>
            </ul>
        );
    }

    return (
        <header className={styles.header}>
            <div className={styles.header_Logo}>
                <a href="#"><img src="/logoLongUCA.webp" alt="Université Clermont Auvergne"/></a>
            </div>
            <nav className={styles.header_Nav}>
                {navContent}
            </nav>
        </header>
    );
}