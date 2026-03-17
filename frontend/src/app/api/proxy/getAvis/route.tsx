import { NextResponse } from 'next/server';

export async function POST(req: any) {
    try {
        const body = await req.json();
        console.log(body);
        const response = await fetch("http://webserver:80/api/avis", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "x-api-key": process.env.API_KEY,
            },
            body: JSON.stringify(body),
        });
        console.log(response);
        if (!response.ok) {
            return NextResponse.json(
                { error: "Erreur backend" },
                { status: response.status }
            );
        }
        const data = await response.json();
        return NextResponse.json(data);
    } catch (error) {
        console.error("Proxy POST Error:", error);
        return NextResponse.json(
            { error: "Erreur interne serveur" },
            { status: 500 }
        );
    }
}

export async function GET() {
    try {

    // 2. Appel au backend avec fetch
    const response = await fetch("http://webserver:80/api/avis", {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            // Injecte tes secrets ici (ils ne seront jamais vus par le client)
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