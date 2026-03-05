'use client'
import styles from './styles.module.scss';
import Link from "next/link";

export default function ButtonTransparent({ texte, lien }) {

    return (
        <button className={styles.button}>
            <Link href={lien}>
            <p>{texte}</p>
            </Link>
        </button>
    );
}