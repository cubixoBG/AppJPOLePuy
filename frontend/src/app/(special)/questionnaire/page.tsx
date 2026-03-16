"use client"

import styles from "./page.module.scss";
import { useEffect, useState } from 'react';

export default function Page() {

    const [rating, setRating] = useState("");
    const [comment, setComment] = useState("");

    const handleSubmit = async (e: any) => {
        e.preventDefault();

        const data = {
            note: Number(rating),
            commentaire: comment
        };

        const response = await fetch("/api/proxy/getAvis", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify(data),
        });

        // reset du formulaire
        setRating("");
        setComment("");
    };

    return(
        <main className={styles.questionnaire}>
        <form onSubmit={handleSubmit}>

            {/* Rating */}
            <div>
                <p>Votre note :</p>
                {[1,2,3,4,5].map((value) => (
                    <label key={value}>
                        <input
                            type="radio"
                            name="rating"
                            value={value}
                            checked={rating === String(value)}
                            onChange={(e) => setRating(e.target.value)}
                        />
                        {value}
                    </label>
                ))}
            </div>

            {/* Commentaire */}
            <div>
                <label>
                    Commentaire :
                    <textarea
                        value={comment}
                        onChange={(e) => setComment(e.target.value)}
                        placeholder="Votre commentaire..."
                    />
                </label>
            </div>

            {/* Bouton */}
            <button type="submit">
                Envoyer
            </button>

        </form>
    </main>
    )
}