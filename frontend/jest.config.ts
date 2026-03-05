import type { Config } from "jest";
import nextJest from "next/jest.js";

const createJestConfig = nextJest({ dir: "./" });

const config: Config = {
  // ── Environnement ───────────────────────────────────────────────
  testEnvironment: "jest-environment-jsdom",

  // ── Setup après l'environnement ──────────────────────────────────
  setupFilesAfterFramework: ["<rootDir>/jest.setup.ts"],

  // ── Résolution des alias @/ ──────────────────────────────────────
  moduleNameMapper: {
    "^@/(.*)$": "<rootDir>/src/$1",
    "^@/../(.*)$": "<rootDir>/$1",
  },

  // ── Couverture (Coverage) ────────────────────────────────────────
  collectCoverage: true,
  collectCoverageFrom: [
    "src/**/*.{ts,tsx}",
    "!src/**/*.d.ts",
    "!src/app/layout.tsx",   // layout Next.js → peu de logique testable
  ],
  coverageThreshold: {
    global: {
      branches: 70,
      functions: 70,
      lines: 70,
      statements: 70,
    },
  },
  coverageReporters: ["text", "lcov", "html"],
  coverageDirectory: "coverage",

  // ── Pattern des fichiers de test ─────────────────────────────────
  testMatch: [
    "**/__tests__/**/*.{ts,tsx}",
    "**/*.test.{ts,tsx}",
    "**/*.spec.{ts,tsx}",
  ],

  // ── Timeout (MSW peut être un peu lent) ─────────────────────────
  testTimeout: 10000,
};

export default createJestConfig(config);