


export default async function Page() {
    const response = await fetch("http://webserver:80/api/departements", {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'x-api-key': process.env.API_KEY,
        },
        cache: 'no-store', 
    })

    // 4. On récupère les données
    const data = await response.json();

    const deps = data.members;

    return (
        <div>
            {deps.map((d : any) => <li key={d.id}>{d.logo} {d.nom} {d.description} {d.responsable}</li>)}
        </div>
    )
}