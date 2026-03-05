/**
 * SCRUM-74 — Conformité des requêtes
 *
 * Vérifie que les appels API depuis le frontend Next.js :
 *  - envoient les bonnes méthodes HTTP et headers
 *  - traitent correctement les réponses (200, 201, 404, 422…)
 *  - exposent les bons champs dans les composants
 *
 * Stack : Jest + React Testing Library + MSW (Mock Service Worker)
 *
 * Méthode TDD :
 *  1. RED   → le test échoue (composant / hook inexistant)
 *  2. GREEN → on implémente le minimum pour passer
 *  3. REFACTOR → on nettoie sans régressions
 */

import { render, screen, waitFor } from "@testing-library/react";
import userEvent from "@testing-library/user-event";
import { http, HttpResponse } from "msw";
import { setupServer } from "msw/node";

// ─── À adapter selon votre arborescence ────────────────────────────────────────
import UtilisateurCard from "@/components/UtilisateurCard";
import JourneesImmersionList from "@/components/JourneesImmersionList";
import EdtCard from "@/components/EdtCard";
import ContactCard from "@/components/ContactCard";
import InscriptionForm from "@/components/InscriptionForm";
import { getUtilisateur, createUtilisateur } from "@/lib/api/utilisateurs";
import { getJourneesImmersion } from "@/lib/api/journeesImmersion";
// ───────────────────────────────────────────────────────────────────────────────

// ════════════════════════════════════════════════════════════════════
// FIXTURES
// ════════════════════════════════════════════════════════════════════

const mockUtilisateur = {
  id: "1",
  nom: "Dupont",
  prenom: "Jean",
  email: "jean.dupont@test.fr",
  tel: "0600000001",
  etablissement: "Lycée A",
  departement: "Informatique",
  type: "visiteur",
  statutEtu: "lycéen",
  heure_arriver: "09:00",
  // mdp NE doit PAS apparaître dans la réponse
};

const mockJournee = {
  id: "1",
  date: "2025-05-10",
  id_utilisateur: "1",
  id_edt: "1",
};

const mockEdt = {
  id: "1",
  QrCode: "https://qr.example.com/edt-1",
};

const mockContact = {
  id: "1",
  Mail: "contact@iut.fr",
  Type: "Présentateur",
  Domaine: "Informatique",
  Img: "/img/contact1.jpg",
};

// ════════════════════════════════════════════════════════════════════
// MSW — Serveur de mocks HTTP
// ════════════════════════════════════════════════════════════════════

const server = setupServer(
  // Utilisateurs
  http.get("/api/utilisateurs", () =>
    HttpResponse.json([mockUtilisateur])
  ),
  http.get("/api/utilisateurs/1", () =>
    HttpResponse.json(mockUtilisateur)
  ),
  http.get("/api/utilisateurs/999", () =>
    HttpResponse.json({ message: "Utilisateur non trouvé" }, { status: 404 })
  ),
  http.post("/api/utilisateurs", () =>
    HttpResponse.json(mockUtilisateur, {
      status: 201,
      headers: { Location: "/api/utilisateurs/1" },
    })
  ),

  // Journées d'immersion
  http.get("/api/journees-immersion", () =>
    HttpResponse.json([mockJournee])
  ),
  http.get("/api/journees-immersion/1", () =>
    HttpResponse.json(mockJournee)
  ),

  // EDT
  http.get("/api/edt/1", () =>
    HttpResponse.json(mockEdt)
  ),

  // Contacts
  http.get("/api/contacts/1", () =>
    HttpResponse.json(mockContact)
  )
);

beforeAll(() => server.listen());
afterEach(() => server.resetHandlers());
afterAll(() => server.close());

// ════════════════════════════════════════════════════════════════════
// 1. COMPOSANT UtilisateurCard
// ════════════════════════════════════════════════════════════════════

describe("SCRUM-74 | UtilisateurCard — affichage des données", () => {
  it("affiche le nom et prénom de l'utilisateur", async () => {
    render(<UtilisateurCard utilisateur={mockUtilisateur} />);

    expect(screen.getByText(/Dupont/i)).toBeInTheDocument();
    expect(screen.getByText(/Jean/i)).toBeInTheDocument();
  });

  it("affiche l'email de l'utilisateur", () => {
    render(<UtilisateurCard utilisateur={mockUtilisateur} />);

    expect(screen.getByText(/jean\.dupont@test\.fr/i)).toBeInTheDocument();
  });

  it("n'affiche PAS le mot de passe dans le rendu", () => {
    const utilisateurAvecMdp = { ...mockUtilisateur, mdp: "SecretPassword!" };
    render(<UtilisateurCard utilisateur={utilisateurAvecMdp} />);

    expect(screen.queryByText(/SecretPassword!/i)).not.toBeInTheDocument();
    expect(screen.queryByText(/mdp/i)).not.toBeInTheDocument();
  });

  it("affiche le type de l'utilisateur (visiteur / ambassadeur / admin)", () => {
    render(<UtilisateurCard utilisateur={mockUtilisateur} />);

    expect(screen.getByText(/visiteur/i)).toBeInTheDocument();
  });
});

// ════════════════════════════════════════════════════════════════════
// 2. HOOK / FETCH — getUtilisateur
// ════════════════════════════════════════════════════════════════════

describe("SCRUM-74 | API getUtilisateur", () => {
  it("retourne les données d'un utilisateur existant (200)", async () => {
    const data = await getUtilisateur("1");

    expect(data.id).toBe("1");
    expect(data.nom).toBe("Dupont");
    expect(data).not.toHaveProperty("mdp");
  });

  it("lève une erreur si l'utilisateur n'existe pas (404)", async () => {
    await expect(getUtilisateur("999")).rejects.toThrow(/404|non trouvé/i);
  });

  it("envoie bien une requête GET avec Content-Type application/json", async () => {
    let capturedRequest: Request | undefined;

    server.use(
      http.get("/api/utilisateurs/1", ({ request }) => {
        capturedRequest = request;
        return HttpResponse.json(mockUtilisateur);
      })
    );

    await getUtilisateur("1");

    expect(capturedRequest?.headers.get("Accept")).toContain("application/json");
  });
});

// ════════════════════════════════════════════════════════════════════
// 3. LISTE DES JOURNÉES D'IMMERSION
// ════════════════════════════════════════════════════════════════════

describe("SCRUM-74 | JourneesImmersionList — conformité affichage", () => {
  it("affiche les journées récupérées depuis l'API", async () => {
    render(<JourneesImmersionList />);

    await waitFor(() => {
      expect(screen.getByText(/2025-05-10/i)).toBeInTheDocument();
    });
  });

  it("affiche un message d'erreur si l'API retourne une erreur", async () => {
    server.use(
      http.get("/api/journees-immersion", () =>
        HttpResponse.json({ message: "Erreur serveur" }, { status: 500 })
      )
    );

    render(<JourneesImmersionList />);

    await waitFor(() => {
      expect(screen.getByRole("alert")).toBeInTheDocument();
    });
  });

  it("affiche un loader pendant le chargement", () => {
    render(<JourneesImmersionList />);

    // Un spinner ou texte de chargement doit être présent initialement
    expect(
      screen.getByRole("status") || screen.getByText(/chargement/i)
    ).toBeTruthy();
  });
});

// ════════════════════════════════════════════════════════════════════
// 4. COMPOSANT EdtCard
// ════════════════════════════════════════════════════════════════════

describe("SCRUM-74 | EdtCard — champs obligatoires", () => {
  it("affiche le QrCode de l'EDT", () => {
    render(<EdtCard edt={mockEdt} />);

    // Le QrCode doit être dans un attribut src ou href
    const qrElement =
      screen.getByRole("img", { name: /qrcode/i }) ||
      screen.getByAltText(/qrcode/i);

    expect(qrElement).toBeInTheDocument();
  });

  it("affiche l'id de l'EDT", () => {
    render(<EdtCard edt={mockEdt} />);

    expect(screen.getByText(/1/)).toBeInTheDocument();
  });
});

// ════════════════════════════════════════════════════════════════════
// 5. COMPOSANT ContactCard
// ════════════════════════════════════════════════════════════════════

describe("SCRUM-74 | ContactCard — tous les champs présents", () => {
  it("affiche le mail, le type et le domaine du contact", () => {
    render(<ContactCard contact={mockContact} />);

    expect(screen.getByText(/contact@iut\.fr/i)).toBeInTheDocument();
    expect(screen.getByText(/Présentateur/i)).toBeInTheDocument();
    expect(screen.getByText(/Informatique/i)).toBeInTheDocument();
  });

  it("affiche l'image du contact avec un attribut alt", () => {
    render(<ContactCard contact={mockContact} />);

    const img = screen.getByRole("img");
    expect(img).toHaveAttribute("src", expect.stringContaining("contact1"));
    expect(img).toHaveAttribute("alt");
  });
});

// ════════════════════════════════════════════════════════════════════
// 6. FORMULAIRE D'INSCRIPTION — createUtilisateur
// ════════════════════════════════════════════════════════════════════

describe("SCRUM-74 | InscriptionForm — envoi correct de la requête POST", () => {
  it("envoie les données du formulaire et affiche un message de succès", async () => {
    const user = userEvent.setup();
    render(<InscriptionForm />);

    await user.type(screen.getByLabelText(/nom/i), "Martin");
    await user.type(screen.getByLabelText(/prénom/i), "Alice");
    await user.type(screen.getByLabelText(/email/i), "alice@test.fr");
    await user.click(screen.getByRole("button", { name: /s'inscrire/i }));

    await waitFor(() => {
      expect(screen.getByText(/inscription réussie/i)).toBeInTheDocument();
    });
  });

  it("affiche les erreurs de validation si les champs sont vides", async () => {
    const user = userEvent.setup();
    render(<InscriptionForm />);

    await user.click(screen.getByRole("button", { name: /s'inscrire/i }));

    await waitFor(() => {
      // Au moins une erreur doit s'afficher
      expect(screen.getAllByRole("alert").length).toBeGreaterThan(0);
    });
  });

  it("POST /api/utilisateurs envoie bien le Content-Type application/json", async () => {
    let capturedContentType = "";

    server.use(
      http.post("/api/utilisateurs", ({ request }) => {
        capturedContentType = request.headers.get("Content-Type") ?? "";
        return HttpResponse.json(mockUtilisateur, { status: 201 });
      })
    );

    await createUtilisateur({
      nom: "Test",
      prenom: "Test",
      email: "t@t.fr",
      tel: "0600000099",
      etablissement: "X",
      departement: "X",
      mdp: "MotDePasse1!",
      type: "visiteur",
      statutEtu: "lycéen",
    });

    expect(capturedContentType).toContain("application/json");
  });
});
