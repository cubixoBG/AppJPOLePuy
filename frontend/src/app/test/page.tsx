'use client';

import { useEffect, useState } from 'react';

export default function Posts() {
    const [deps, setDeps] = useState([]);

    useEffect(() => {
        fetch('/api/proxy/getDeps') // <= configurer les appels api dans le proxy
            .then(res => res.json())
            .then(data => {
                setDeps(data.member);
            })
            .catch(err => console.error("Erreur fetch :", err));},
    []);

    return (
        <ul>
            {deps.map((d : any) => <li key={d.id}>{d.nom}</li>)}
        </ul>
    );
}