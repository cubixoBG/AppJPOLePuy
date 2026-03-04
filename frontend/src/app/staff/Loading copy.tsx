"use client";

export default function Loading() {
  return (
    <div className="loading-wrapper">
      <div className="spinner">
        <span></span>
        <span></span>
        <span></span>
      </div>
      <p className="loading-text">Chargement en cours…</p>

     
    </div>
  );
}
