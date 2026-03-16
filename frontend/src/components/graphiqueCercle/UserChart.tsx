'use client'
import { useEffect, useRef } from "react";
import Chart from "chart.js/auto";

interface Props {
    labels: string[];
    counts: number[];
}

export default function UserChart({ labels, counts }: Props) {
    const chartRef = useRef(null);
    const chartInstanceRef = useRef < Chart | null > (null);

    useEffect(() => {
        if (chartInstanceRef.current) {
            chartInstanceRef.current.destroy();
        }

        if (!chartRef.current) return;

        const ctx = chartRef.current.getContext("2d");;

        chartInstanceRef.current = new Chart(ctx, {
            type: "pie",
            data: {
                labels,
                datasets: [{
                    data: counts,
                    borderWidth: 1,
                }],
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: "bottom" },
                },
            },
        });

        return () => {
            if (chartInstanceRef.current) {
                chartInstanceRef.current.destroy();
            }
        };
    }, [labels, counts]);

    return <canvas ref={chartRef} />;
}