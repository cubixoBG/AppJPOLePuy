'use client'
import { useEffect, useRef } from "react";
import Chart from "chart.js/auto";

interface Props {
  mmiCount: number;
  infoCount: number;
  chimieCount: number;
}

export default function UserChart({ mmiCount, infoCount, chimieCount }: Props) {
  const chartRef = useRef(null);
  const chartInstanceRef = useRef<Chart | null>(null);

  useEffect(() => {
    if (chartInstanceRef.current) {
      chartInstanceRef.current.destroy();
    }

    const ctx = chartRef.current.getContext("2d");

    chartInstanceRef.current = new Chart(ctx, {
      type: "bar",
      data: {
        labels: ["MMI", "Informatique", "Chimie"],
        datasets: [{
          label: "Nombre d'étudiants",
          data: [mmiCount, infoCount, chimieCount],
          borderWidth: 1,
        }],
      },
      options: {
        scales: { y: { beginAtZero: true } },
      },
    });

    return () => {
      if (chartInstanceRef.current) {
        chartInstanceRef.current.destroy();
      }
    };
  }, [mmiCount, infoCount, chimieCount]); // ✅ dépendances ajoutées

  return <canvas ref={chartRef} />;
}