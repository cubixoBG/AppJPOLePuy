'use client'
import styles from './styles.module.scss'

export default function ButtonFull({ texte, lien }) {

    
    return (
        <a href={lien} className={styles.button}>
            {texte}
        </a>
    );
}