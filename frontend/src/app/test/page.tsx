import React from "react";

export default async function TestPage() {
    let responseData: any = null;

    try {
    const response = await fetch("http://webserver:80/api/avis", {
        method: "POST",
        headers: {
        "Accept": "application/ld+json",
        "Content-Type": "application/ld+json",
    },
        body: JSON.stringify({
        visiteur: "/api/users/11735",
        commentaire: "string",
        date: "2026-03-04T13:11:55.420Z",
        note: 0,
        }),
    });

    responseData = await response.json();
    } catch (error) {
        console.error("Erreur serveur :", error);
        responseData = { error: "Impossible de contacter l'API" };
    }

    return (
        <div>
            <h1>Résultat du POST côté serveur</h1>
            <pre>{JSON.stringify(responseData, null, 2)}</pre>
        </div>
    );
}