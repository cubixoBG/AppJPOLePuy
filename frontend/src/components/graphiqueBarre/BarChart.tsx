import UserChart from "./UserChart";

export default async function BarChart() {
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

  const filter = (dept, string) => users.filter((u, any) =>
    u.type !== "Admin" && u.type !== "Presentateur" && u.departement === dept
  ).length;

  return (
    <UserChart
      mmiCount={filter("MMI")}
      infoCount={filter("Informatique")}
      chimieCount={filter("Chimie")}
    />
  );
}