import { NextResponse } from 'next/server';

export async function POST(req: any) {
    try {
        const body = await req.json();
        console.log(body);
        const response = await fetch("http://webserver:80/api/notifications/send", {
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