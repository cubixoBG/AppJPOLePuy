'use client'
import Link from "next/link";
import styles from "./styles.module.scss";

export default function ButtonFull({ texte, lien, color = 1 }) {
    return (
        <button className={styles.button} data-color={color}>
            <Link href={lien}>
                <p>{texte}</p>
            </Link>
        </button>
    );
}