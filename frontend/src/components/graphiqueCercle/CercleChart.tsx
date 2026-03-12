import EtablissementChart from "./UserChart";

export default async function CercleChart() {
  const response = await fetch("http://webserver:80/api/users", {
    method: 'GET',
    headers: {
      'Content-Type': 'application/json',
      'x-api-key': process.env.API_KEY || "",
    },
    cache: 'no-store',
  });

  const data = await response.json();
  const users = data.member || [];

  const counts: Record<string, number> = {};
  users
    .filter((u: any) => u.type !== "Admin" && u.type !== "Presentateur")
    .forEach((u: any) => {
      const etab = u.etablissement || "Inconnu";
      counts[etab] = (counts[etab] ?? 0) + 1;
    });

  // Trier par count décroissant
  const sorted = Object.entries(counts).sort((a, b) => b[1] - a[1]);

  const labels = sorted.map(([etab]) => etab);
  const values = sorted.map(([, count]) => count);

  return <EtablissementChart labels={labels} counts={values} />;
}