'use client'
import styles from './styles.module.scss'

export default function ButtonTransparent({ texte, lien }) {

    
    return (
        <a href={lien} className={styles.button}>
            {texte}
        </a>
    );
}