"use client"
import { useState, useEffect } from "react";


export default function DepartementCards() {

    const [deps, setDeps] = useState([]);

    useEffect(() => {
        fetch('/api/proxy/getDeps')
            .then(res => res.json())
            .then(data => {
                setDeps(data.member);
            })
            .catch(err => console.error("Erreur fetch :", err));},
    []);

    console.log(deps);

    return (
        <div>
            {deps.map((d : any) => (
                <a>
                    <article>
                        <p>{d.logo}</p>
                        <h3>{d.nom}</h3>
                        <p>{d.description}</p>
                    </article>
                </a>
            ))}
        </div>
    )
}
