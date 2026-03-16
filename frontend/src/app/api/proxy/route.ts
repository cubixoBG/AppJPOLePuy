// import { NextRequest, NextResponse } from "next/server";
// import { cookies } from "next/headers";
// import proxy from "@src/proxy";

// export async function POST(request: NextRequest) {
//   const { password } = await request.json();

//   const data = await proxy("/users");
//   const users = data.member || [];
//   const adminUser = users.find((u: any) => u.type === "Admin");

//   if (!adminUser || adminUser.password !== password) {
//     return NextResponse.json({ success: false }, { status: 401 });
//   }

//   // Pas de maxAge → cookie de session, supprimé à la fermeture du navigateur
//   const cookieStore = await cookies();
//   cookieStore.set("admin_session", "authenticated", {
//     httpOnly: true,
//     secure: process.env.NODE_ENV === "production",
//     sameSite: "strict",
//     path: "/",
//   });

//   return NextResponse.json({ success: true });
// }