"use client";

import { useEffect, useState } from "react";
import { useRouter } from "next/navigation";

export default function Dashboard() {

    const router = useRouter();

    const [data, setData] = useState<any>(null);

    useEffect(() => {

        const token = localStorage.getItem("token");

        if (!token) {
            router.push("/login");
            return;
        }

        fetch("http://localhost:8000/api/admin/dashboard", {
            headers: {
                Authorization: "Bearer " + token
            }
        })
            .then(res => res.json())
            .then(setData);

    }, []);

    return (
        <div>

            <h1>Admin Dashboard</h1>

            {!data && <p>Loading...</p>}

            {data && (
                <div>

                    <h2>Visiteurs</h2>
                    <pre>{JSON.stringify(data.visitors, null, 2)}</pre>

                    <h2>Ambassadeurs</h2>
                    <pre>{JSON.stringify(data.ambassadors, null, 2)}</pre>

                </div>
            )}

        </div>
    );
}