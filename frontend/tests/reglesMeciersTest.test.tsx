/**
 * SCRUM-75 — Règles métiers
 *
 * Vérifie les règles de gestion applicatives côté frontend :
 *  - Inscription visiteur à une journée d'immersion
 *  - Contraintes de rôle dans les composants (visiteur / ambassadeur / admin)
 *  - Questionnaire de satisfaction ambassadeur
 *  - Dashboard admin
 *
 * Stack : Jest + React Testing Library + MSW
 *
 * Méthode TDD :
 *  1. RED   → le test échoue, la règle métier n'est pas encore codée
 *  2. GREEN → on code la règle minimale pour passer le test
 *  3. REFACTOR → on nettoie sans régressions
 */

import { render, screen, waitFor } from "@testing-library/react";
import userEvent from "@testing-library/user-event";
import { http, HttpResponse } from "msw";
import { setupServer } from "msw/node";

// ─── À adapter selon votre arborescence ────────────────────────────────────────
import InscriptionJourneeForm from "@/components/InscriptionJourneeForm";
import QuestionnaireSatisfaction from "@/components/QuestionnaireSatisfaction";
import AdminDashboard from "@/components/AdminDashboard";
import EspaceAmbassadeur from "@/components/EspaceAmbassadeur";
import { validateInscription } from "@/lib/validation/inscription";
import { validateMotDePasse } from "@/lib/validation/motDePasse";
// ───────────────────────────────────────────────────────────────────────────────

// ════════════════════════════════════════════════════════════════════
// FIXTURES
// ════════════════════════════════════════════════════════════════════

const mockJourneesDisponibles = [
  { id: "1", date: "2025-05-10", id_utilisateur: null, id_edt: "1" },
  { id: "2", date: "2025-06-15", id_utilisateur: null, id_edt: "2" },
];

const mockAmbassadeurs = [
  { id: "3", nom: "Leclercq", prenom: "Marc", type: "ambassadeur", disponibilite: true },
];

const mockDashboard = {
  visiteurs: [{ id: "2", nom: "Dupont", prenom: "Jean" }],
  ambassadeurs: mockAmbassadeurs,
  statistiques: { nombre_presents: 12 },
};

// ════════════════════════════════════════════════════════════════════
// MSW — Serveur de mocks HTTP
// ════════════════════════════════════════════════════════════════════

const server = setupServer(
  http.get("/api/journees-immersion", () =>
    HttpResponse.json(mockJourneesDisponibles)
  ),
  http.post("/api/journees-immersion", () =>
    HttpResponse.json({ id: "10" }, { status: 201 })
  ),
  http.get("/api/admin/dashboard", () =>
    HttpResponse.json(mockDashboard)
  ),
  http.get("/api/ambassadeur/espace", () =>
    HttpResponse.json({ informations_visite: ["Salle A", "Salle B"] })
  ),
  http.post("/api/questionnaires", () =>
    HttpResponse.json({ id: "5" }, { status: 201 })
  )
);

beforeAll(() => server.listen());
afterEach(() => server.resetHandlers());
afterAll(() => server.close());

// ════════════════════════════════════════════════════════════════════
// 1. INSCRIPTION VISITEUR — RÈGLES MÉTIERS
// ════════════════════════════════════════════════════════════════════

describe("SCRUM-75 | InscriptionJourneeForm — règles d'inscription", () => {
  it("affiche les journées disponibles au visiteur", async () => {
    render(<InscriptionJourneeForm utilisateurId="2" />);

    await waitFor(() => {
      expect(screen.getByText(/2025-05-10/i)).toBeInTheDocument();
      expect(screen.getByText(/2025-06-15/i)).toBeInTheDocument();
    });
  });

  it("soumet l'inscription avec succès et affiche une confirmation", async () => {
    const user = userEvent.setup();
    render(<InscriptionJourneeForm utilisateurId="2" />);

    await waitFor(() =>
      screen.getByText(/2025-05-10/i)
    );

    await user.click(screen.getByRole("radio", { name: /2025-05-10/i }));
    await user.click(screen.getByRole("button", { name: /s'inscrire/i }));

    await waitFor(() => {
      expect(screen.getByText(/inscription confirmée/i)).toBeInTheDocument();
    });
  });

  it("affiche une erreur si le visiteur est déjà inscrit à cette journée (409)", async () => {
    server.use(
      http.post("/api/journees-immersion", () =>
        HttpResponse.json(
          { message: "Vous êtes déjà inscrit à cette journée." },
          { status: 409 }
        )
      )
    );

    const user = userEvent.setup();
    render(<InscriptionJourneeForm utilisateurId="2" />);

    await waitFor(() => screen.getByText(/2025-05-10/i));
    await user.click(screen.getByRole("radio", { name: /2025-05-10/i }));
    await user.click(screen.getByRole("button", { name: /s'inscrire/i }));

    await waitFor(() => {
      expect(screen.getByText(/déjà inscrit/i)).toBeInTheDocument();
    });
  });

  it("désactive le bouton d'inscription si aucune journée n'est sélectionnée", async () => {
    render(<InscriptionJourneeForm utilisateurId="2" />);

    await waitFor(() => screen.getByText(/2025-05-10/i));

    const btn = screen.getByRole("button", { name: /s'inscrire/i });
    expect(btn).toBeDisabled();
  });
});

// ════════════════════════════════════════════════════════════════════
// 2. VALIDATION CÔTÉ CLIENT — validateInscription
// ════════════════════════════════════════════════════════════════════

describe("SCRUM-75 | validateInscription — règles de validation", () => {
  it("retourne une erreur si la date est dans le passé", () => {
    const result = validateInscription({ date: "2020-01-01", id_edt: "1" });

    expect(result.valid).toBe(false);
    expect(result.errors.date).toMatch(/passée/i);
  });

  it("retourne une erreur si id_edt est manquant", () => {
    const result = validateInscription({ date: "2025-06-15", id_edt: "" });

    expect(result.valid).toBe(false);
    expect(result.errors.id_edt).toBeTruthy();
  });

  it("retourne valid:true si toutes les données sont correctes", () => {
    const result = validateInscription({ date: "2025-06-15", id_edt: "1" });

    expect(result.valid).toBe(true);
    expect(result.errors).toEqual({});
  });
});

// ════════════════════════════════════════════════════════════════════
// 3. QUESTIONNAIRE DE SATISFACTION AMBASSADEUR
// ════════════════════════════════════════════════════════════════════

describe("SCRUM-75 | QuestionnaireSatisfaction — règles métiers", () => {
  it("soumet le questionnaire avec une note valide (1–5)", async () => {
    const user = userEvent.setup();
    render(<QuestionnaireSatisfaction ambassadeurId="3" />);

    // Sélectionner la note 4
    await user.click(screen.getByRole("radio", { name: /4/i }));
    await user.type(
      screen.getByRole("textbox", { name: /commentaire/i }),
      "Bonne journée !"
    );
    await user.click(screen.getByRole("button", { name: /envoyer/i }));

    await waitFor(() => {
      expect(screen.getByText(/merci/i)).toBeInTheDocument();
    });
  });

  it("affiche une erreur si aucune note n'est sélectionnée", async () => {
    const user = userEvent.setup();
    render(<QuestionnaireSatisfaction ambassadeurId="3" />);

    await user.click(screen.getByRole("button", { name: /envoyer/i }));

    await waitFor(() => {
      expect(screen.getByText(/note.*obligatoire/i)).toBeInTheDocument();
    });
  });

  it("n'affiche que 5 étoiles / options de note maximum", () => {
    render(<QuestionnaireSatisfaction ambassadeurId="3" />);

    const notes = screen.getAllByRole("radio");
    expect(notes.length).toBe(5);
  });

  it("affiche une erreur API si le serveur refuse la soumission (422)", async () => {
    server.use(
      http.post("/api/questionnaires", () =>
        HttpResponse.json({ message: "Note invalide." }, { status: 422 })
      )
    );

    const user = userEvent.setup();
    render(<QuestionnaireSatisfaction ambassadeurId="3" />);

    await user.click(screen.getAllByRole("radio")[0]);
    await user.click(screen.getByRole("button", { name: /envoyer/i }));

    await waitFor(() => {
      expect(screen.getByRole("alert")).toBeInTheDocument();
    });
  });
});

// ════════════════════════════════════════════════════════════════════
// 4. ESPACE AMBASSADEUR
// ════════════════════════════════════════════════════════════════════

describe("SCRUM-75 | EspaceAmbassadeur — contenu attendu", () => {
  it("affiche les informations de visite et un rappel de donner un avis", async () => {
    render(<EspaceAmbassadeur ambassadeurId="3" />);

    await waitFor(() => {
      expect(screen.getByText(/Salle A/i)).toBeInTheDocument();
      // CTA "donner votre avis" doit être présent selon le syllabus
      expect(
        screen.getByRole("link", { name: /avis/i }) ||
        screen.getByRole("button", { name: /avis/i })
      ).toBeTruthy();
    });
  });
});

// ════════════════════════════════════════════════════════════════════
// 5. DASHBOARD ADMIN
// ════════════════════════════════════════════════════════════════════

describe("SCRUM-75 | AdminDashboard — règles d'affichage", () => {
  it("affiche la liste des visiteurs", async () => {
    render(<AdminDashboard />);

    await waitFor(() => {
      expect(screen.getByText(/Dupont/i)).toBeInTheDocument();
    });
  });

  it("affiche les ambassadeurs avec leurs disponibilités", async () => {
    render(<AdminDashboard />);

    await waitFor(() => {
      expect(screen.getByText(/Leclercq/i)).toBeInTheDocument();
      // La disponibilité doit être visible
      expect(screen.getByText(/disponible/i)).toBeInTheDocument();
    });
  });

  it("affiche le nombre de personnes présentes dans les statistiques", async () => {
    render(<AdminDashboard />);

    await waitFor(() => {
      expect(screen.getByText(/12/)).toBeInTheDocument();
    });
  });

  it("affiche un message si l'API du dashboard est indisponible", async () => {
    server.use(
      http.get("/api/admin/dashboard", () =>
        HttpResponse.json({ message: "Erreur serveur" }, { status: 500 })
      )
    );

    render(<AdminDashboard />);

    await waitFor(() => {
      expect(screen.getByRole("alert")).toBeInTheDocument();
    });
  });
});

// ════════════════════════════════════════════════════════════════════
// 6. RÈGLE — VALIDATION DU MOT DE PASSE CÔTÉ CLIENT
// ════════════════════════════════════════════════════════════════════

describe("SCRUM-75 | validateMotDePasse — règles de complexité", () => {
  it("rejette un mot de passe de moins de 8 caractères", () => {
    expect(validateMotDePasse("Aa1!")).toMatchObject({ valid: false });
  });

  it("rejette un mot de passe sans majuscule", () => {
    expect(validateMotDePasse("motdepasse1!")).toMatchObject({ valid: false });
  });

  it("rejette un mot de passe sans chiffre", () => {
    expect(validateMotDePasse("MotDePasse!")).toMatchObject({ valid: false });
  });

  it("rejette un mot de passe sans caractère spécial", () => {
    expect(validateMotDePasse("MotDePasse1")).toMatchObject({ valid: false });
  });

  it("accepte un mot de passe fort (8 car., majuscule, chiffre, spécial)", () => {
    expect(validateMotDePasse("MotDePasse1!")).toMatchObject({ valid: true });
  });
});
