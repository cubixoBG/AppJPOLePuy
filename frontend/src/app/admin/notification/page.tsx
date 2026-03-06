"use client";
import { useState } from "react";
import styles from "./notification.module.css";
export default function NotificationPage() {
  const [msg, setMsg] = useState("");
  const [cible, setCible] = useState("ambassadeurs");
  const [sent, setSent] = useState(false);
  function handleSend(e: React.FormEvent) {
    e.preventDefault();
    setSent(true);
    setMsg("");
    setTimeout(() => setSent(false), 3000);
  }
  return (
    <div className={styles.page}>
      <h1 className={styles.title}>Notifications</h1>
      <p className={styles.sub}>Envoyer un message aux ambassadeurs ou visiteurs</p>
      {sent && <div className={styles.success}>Notification envoyée avec succès !</div>}
      <div className={styles.formCard}>
        <form onSubmit={handleSend} className={styles.form}>
          <div className={styles.field}>
            <label>Destinataires</label>
            <div className={styles.chips}>
              {["ambassadeurs","visiteurs","tous"].map((c) => (
                <button type="button" key={c} className={cible===c?styles.chipActive:styles.chip} onClick={()=>setCible(c)}>
                  {c.charAt(0).toUpperCase()+c.slice(1)}
                </button>
              ))}
            </div>
          </div>
          <div className={styles.field}>
            <label htmlFor="msg">Message</label>
            <textarea id="msg" rows={5} value={msg} onChange={(e)=>setMsg(e.target.value)} placeholder="Votre message..." required className={styles.textarea}/>
          </div>
          <button type="submit" className={styles.sendBtn} disabled={!msg.trim()}>Envoyer la notification</button>
        </form>
      </div>
    </div>
  );
}
