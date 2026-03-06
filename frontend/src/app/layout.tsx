import type { Metadata } from "next";
import "./globals.css";

export const metadata: Metadata = {
  title: "JPO — IUT Clermont-Ferrand",
  description: "Application de gestion des Journées Portes Ouvertes",
};

export default function RootLayout({ children }: { children: React.ReactNode }) {
  return (
    <html lang="fr">
      <body>{children}</body>
    </html>
  );
}
