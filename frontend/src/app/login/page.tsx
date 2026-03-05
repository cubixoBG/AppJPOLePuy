"use client";

import { useState } from "react";
import Link from "next/link";
import styles from "./login.module.css";

export default function LoginPage() {
  const [password, setPassword] = useState("");
  const [error, setError] = useState(false);

  function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    if (password !== "admin") {
      setError(true);
    } else {
      setError(false);
      window.location.href = "/admin";
    }
  }

  return (
    <div className={styles.page}>
      {/* Header */}
      <header className={styles.header}>
        <div className={styles.headerInner}>
          <div className={styles.brand}>
            <div className={styles.logoCircle}>
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                <polyline points="9,22 9,12 15,12 15,22" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
              </svg>
            </div>
            <div>
              <span className={styles.brandName}>IUT Clermont-Ferrand</span>
              <span className={styles.brandSub}>Université Clermont Auvergne</span>
            </div>
          </div>
          <Link href="/" className={styles.backBtn}>Retour au Site</Link>
        </div>
      </header>

      {/* Main */}
      <main className={styles.main}>
        <p className={styles.pageLabel}>Connexion Admin</p>

        <div className={styles.card}>
          <div className={styles.lockIcon}>
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none">
              <rect x="3" y="11" width="18" height="11" rx="2" ry="2" stroke="currentColor" strokeWidth="2"/>
              <path d="M7 11V7a5 5 0 0110 0v4" stroke="currentColor" strokeWidth="2" strokeLinecap="round"/>
            </svg>
          </div>
          <h1 className={styles.cardTitle}>Espace Administration</h1>

          <form onSubmit={handleSubmit} className={styles.form}>
            <div className={styles.fieldWrapper}>
              <input
                type="password"
                placeholder="Entrez votre passe admin"
                value={password}
                onChange={(e) => { setPassword(e.target.value); setError(false); }}
                className={`${styles.input} ${error ? styles.inputError : ""}`}
                aria-label="Mot de passe admin"
              />
              {error && <p className={styles.errorMsg}>Mot de passe incorrect.</p>}
            </div>
            <button type="submit" className={styles.submitBtn}>Se connecter</button>
          </form>
        </div>
      </main>

      {/* Bottom label */}
      <div className={styles.bottomLabel}>
        <span>Espace Admin</span>
      </div>
    </div>
  );
}
