// export default async function proxyFetch(path: string, options?: RequestInit) {
//   const response = await fetch(`http://webserver:80/api${path}`, {
//     ...options,
//     headers: {
//       "Content-Type": "application/json",
//       "x-api-key": process.env.API_KEY || "",
//       ...options?.headers,
//     },
//     cache: "no-store",
//   });
 
//   if (!response.ok) {
//     throw new Error(`API error: ${response.status}`);
//   }
 
//   return response.json();
// }