import { NextResponse } from 'next/server';

export async function GET() {

    const mail = "zdonnelly@example.net";

    try {

    // 2. Appel au backend avec fetch
    const response = await fetch(`http://webserver:80/api/users?mail=zdonnelly@example.net`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'x-api-key': process.env.API_KEY,
        },
        cache: 'no-store', 
    });

    // 3. Gestion d'erreur si le backend répond mal
    if (!response.ok) {
        return NextResponse.json(
            { error: 'Erreur backend' },
            { status: response.status }
        );
    }

    // 4. On récupère les données
    const data = await response.json();

    console.log(data);

    // 5. On renvoie les données au client
    return NextResponse.json(data);

    } catch (error) {
        console.error("Proxy Error:", error);
        return NextResponse.json(
            { error: 'Erreur interne du serveur' },
            { status: 500 }
        );
    }
}