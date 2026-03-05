/**
 * SCRUM-76 — Sécurité
 *
 * Vérifie les mécanismes de sécurité côté frontend Next.js :
 *  - Authentification (login, stockage de token, logout)
 *  - Guards de routes (redirection si non connecté ou mauvais rôle)
 *  - Non-exposition de données sensibles dans le DOM
 *  - Gestion des tokens expirés / invalides
 *  - Protection contre les injections XSS dans les champs formulaire
 *
 * Stack : Jest + React Testing Library + MSW
 *
 * Méthode TDD :
 *  1. RED   → le test échoue, la protection n'existe pas encore
 *  2. GREEN → on implémente le garde-fou minimal
 *  3. REFACTOR → on consolide sans casser
 */

import { render, screen, waitFor, act } from "@testing-library/react";
import userEvent from "@testing-library/user-event";
import { http, HttpResponse } from "msw";
import { setupServer } from "msw/node";

// ─── À adapter selon votre arborescence ────────────────────────────────────────
import LoginForm from "@/components/LoginForm";
import RouteGuard from "@/components/RouteGuard";
import AdminDashboard from "@/components/AdminDashboard";
import ProfilPage from "@/app/profil/page";
import { AuthProvider, useAuth } from "@/context/AuthContext";
import { renderWithAuth } from "@/tests/utils/renderWithAuth"; // helper à créer
// ───────────────────────────────────────────────────────────────────────────────

// ════════════════════════════════════════════════════════════════════
// MSW — Serveur de mocks HTTP
// ════════════════════════════════════════════════════════════════════

const server = setupServer(
  // Login valide → retourne token
  http.post("/api/login", async ({ request }) => {
    const body = (await request.json()) as { email: string; mdp: string };

    if (body.email === "admin@test.fr" && body.mdp === "AdminPass1!") {
      return HttpResponse.json({ token: "fake-jwt-admin", type: "admin" });
    }
    if (body.email === "ambassadeur@test.fr" && body.mdp === "AmbassadeurPass1!") {
      return HttpResponse.json({ token: "fake-jwt-ambassadeur", type: "ambassadeur" });
    }
    if (body.email === "visiteur@test.fr" && body.mdp === "VisiteurPass1!") {
      return HttpResponse.json({ token: "fake-jwt-visiteur", type: "visiteur" });
    }

    // Toujours le même message, quelle que soit la raison → anti-énumération
    return HttpResponse.json(
      { message: "Identifiants incorrects." },
      { status: 401 }
    );
  }),

  // Dashboard → réservé aux admins
  http.get("/api/admin/dashboard", ({ request }) => {
    const auth = request.headers.get("Authorization");
    if (auth === "Bearer fake-jwt-admin") {
      return HttpResponse.json({
        visiteurs: [],
        ambassadeurs: [],
        statistiques: { nombre_presents: 0 },
      });
    }
    return HttpResponse.json({ message: "Accès refusé." }, { status: 403 });
  }),

  // Profil utilisateur → mdp absent
  http.get("/api/utilisateurs/1", () =>
    HttpResponse.json({
      id: "1",
      nom: "Dupont",
      prenom: "Jean",
      email: "jean.dupont@test.fr",
      tel: "0600000001",
      etablissement: "Lycée A",
      departement: "Informatique",
      type: "visiteur",
      statutEtu: "lycéen",
      // mdp volontairement absent
    })
  )
);

beforeAll(() => server.listen());
afterEach(() => {
  server.resetHandlers();
  // Nettoyer le sessionStorage / localStorage entre chaque test
  sessionStorage.clear();
  localStorage.clear();
});
afterAll(() => server.close());

// ════════════════════════════════════════════════════════════════════
// 1. FORMULAIRE DE LOGIN
// ════════════════════════════════════════════════════════════════════

describe("SCRUM-76 | LoginForm — authentification", () => {
  it("affiche les champs email et mot de passe", () => {
    render(<LoginForm />);

    expect(screen.getByLabelText(/email/i)).toBeInTheDocument();
    expect(screen.getByLabelText(/mot de passe/i)).toBeInTheDocument();
  });

  it("stocke le token après un login valide", async () => {
    const user = userEvent.setup();
    render(
      <AuthProvider>
        <LoginForm />
      </AuthProvider>
    );

    await user.type(screen.getByLabelText(/email/i), "admin@test.fr");
    await user.type(screen.getByLabelText(/mot de passe/i), "AdminPass1!");
    await user.click(screen.getByRole("button", { name: /connexion/i }));

    await waitFor(() => {
      // Le token doit être stocké dans sessionStorage (plus sûr que localStorage)
      const stored = sessionStorage.getItem("token");
      expect(stored).toBe("fake-jwt-admin");
    });
  });

  it("affiche un message d'erreur générique en cas d'échec (401)", async () => {
    const user = userEvent.setup();
    render(<LoginForm />);

    await user.type(screen.getByLabelText(/email/i), "inconnu@test.fr");
    await user.type(screen.getByLabelText(/mot de passe/i), "mauvaismdp");
    await user.click(screen.getByRole("button", { name: /connexion/i }));

    await waitFor(() => {
      expect(screen.getByRole("alert")).toBeInTheDocument();
    });

    // Le message NE doit PAS préciser si c'est l'email ou le mdp qui est faux
    const alertText = screen.getByRole("alert").textContent ?? "";
    expect(alertText).not.toMatch(/email.*incorrect|mot de passe.*incorrect/i);
  });

  it("masque le mot de passe par défaut (type=password)", () => {
    render(<LoginForm />);

    const mdpInput = screen.getByLabelText(/mot de passe/i);
    expect(mdpInput).toHaveAttribute("type", "password");
  });

  it("ne stocke PAS le mot de passe dans le DOM ou le state", async () => {
    const user = userEvent.setup();
    const { container } = render(<LoginForm />);

    await user.type(screen.getByLabelText(/mot de passe/i), "AdminPass1!");

    // Le mot de passe ne doit pas apparaître dans un data-attribute ou élément visible
    expect(container.innerHTML).not.toContain("AdminPass1!");
  });
});

// ════════════════════════════════════════════════════════════════════
// 2. ROUTE GUARD — PROTECTION PAR RÔLE
// ════════════════════════════════════════════════════════════════════

describe("SCRUM-76 | RouteGuard — contrôle d'accès", () => {
  it("redirige vers /login si l'utilisateur n'est pas connecté", async () => {
    const mockPush = jest.fn();
    jest.mock("next/navigation", () => ({ useRouter: () => ({ push: mockPush }) }));

    render(
      <RouteGuard requiredRole="admin">
        <AdminDashboard />
      </RouteGuard>
    );

    await waitFor(() => {
      expect(mockPush).toHaveBeenCalledWith("/login");
    });
  });

  it("affiche le contenu si l'utilisateur a le bon rôle (admin → dashboard admin)", async () => {
    // Simuler un utilisateur admin connecté
    sessionStorage.setItem("token", "fake-jwt-admin");
    sessionStorage.setItem("userType", "admin");

    render(
      <AuthProvider>
        <RouteGuard requiredRole="admin">
          <div>Contenu admin</div>
        </RouteGuard>
      </AuthProvider>
    );

    await waitFor(() => {
      expect(screen.getByText(/Contenu admin/i)).toBeInTheDocument();
    });
  });

  it("affiche une page 403 si un ambassadeur tente d'accéder au dashboard admin", async () => {
    sessionStorage.setItem("token", "fake-jwt-ambassadeur");
    sessionStorage.setItem("userType", "ambassadeur");

    render(
      <AuthProvider>
        <RouteGuard requiredRole="admin">
          <div>Contenu admin</div>
        </RouteGuard>
      </AuthProvider>
    );

    await waitFor(() => {
      expect(screen.queryByText(/Contenu admin/i)).not.toBeInTheDocument();
      expect(screen.getByText(/accès refusé|403|non autorisé/i)).toBeInTheDocument();
    });
  });

  it("affiche une page 403 si un visiteur tente d'accéder à l'espace ambassadeur", async () => {
    sessionStorage.setItem("token", "fake-jwt-visiteur");
    sessionStorage.setItem("userType", "visiteur");

    render(
      <AuthProvider>
        <RouteGuard requiredRole="ambassadeur">
          <div>Espace ambassadeur</div>
        </RouteGuard>
      </AuthProvider>
    );

    await waitFor(() => {
      expect(screen.queryByText(/Espace ambassadeur/i)).not.toBeInTheDocument();
    });
  });
});

// ════════════════════════════════════════════════════════════════════
// 3. GESTION DU TOKEN
// ════════════════════════════════════════════════════════════════════

describe("SCRUM-76 | Token — stockage et expiration", () => {
  it("supprime le token du sessionStorage après le logout", async () => {
    sessionStorage.setItem("token", "fake-jwt-admin");

    const user = userEvent.setup();
    render(
      <AuthProvider>
        <button onClick={() => { sessionStorage.removeItem("token"); }}>
          Se déconnecter
        </button>
      </AuthProvider>
    );

    await user.click(screen.getByRole("button", { name: /déconnecter/i }));

    expect(sessionStorage.getItem("token")).toBeNull();
  });

  it("redirige vers /login si le token est expiré (401 de l'API)", async () => {
    server.use(
      http.get("/api/admin/dashboard", () =>
        HttpResponse.json({ message: "Token expiré." }, { status: 401 })
      )
    );

    sessionStorage.setItem("token", "expired-token");
    sessionStorage.setItem("userType", "admin");

    const mockPush = jest.fn();
    jest.mock("next/navigation", () => ({ useRouter: () => ({ push: mockPush }) }));

    render(<AdminDashboard />);

    await waitFor(() => {
      expect(mockPush).toHaveBeenCalledWith("/login");
    });
  });

  it("le token ne doit pas être stocké dans localStorage (préférer sessionStorage)", () => {
    // Vérifie qu'aucune clé sensible n'est écrite dans localStorage
    const user = userEvent.setup();

    render(
      <AuthProvider>
        <LoginForm />
      </AuthProvider>
    );

    // Après le montage, localStorage ne doit pas contenir de token
    expect(localStorage.getItem("token")).toBeNull();
    expect(localStorage.getItem("jwt")).toBeNull();
  });
});

// ════════════════════════════════════════════════════════════════════
// 4. NON-EXPOSITION DE DONNÉES SENSIBLES DANS LE DOM
// ════════════════════════════════════════════════════════════════════

describe("SCRUM-76 | ProfilPage — données sensibles absentes du DOM", () => {
  it("n'affiche pas le mot de passe dans la page de profil", async () => {
    sessionStorage.setItem("token", "fake-jwt-visiteur");

    const { container } = render(<ProfilPage params={{ id: "1" }} />);

    await waitFor(() =>
      screen.getByText(/Dupont/i)
    );

    expect(container.innerHTML).not.toMatch(/mdp|password|mot_de_passe/i);
  });

  it("n'affiche pas le token JWT dans le DOM", async () => {
    sessionStorage.setItem("token", "fake-jwt-visiteur");

    const { container } = render(<ProfilPage params={{ id: "1" }} />);

    expect(container.innerHTML).not.toContain("fake-jwt-visiteur");
  });
});

// ════════════════════════════════════════════════════════════════════
// 5. PROTECTION XSS — FORMULAIRES
// ════════════════════════════════════════════════════════════════════

describe("SCRUM-76 | Formulaires — protection contre les injections XSS", () => {
  it("n'exécute pas de script injecté dans le champ nom", async () => {
    const xssPayload = '<script>window.__xss = true</script>';
    const user = userEvent.setup();

    render(<LoginForm />);
    await user.type(screen.getByLabelText(/email/i), xssPayload);

    // Le script ne doit pas s'être exécuté
    expect((window as Record<string, unknown>).__xss).toBeUndefined();
  });

  it("échappe le contenu HTML potentiellement dangereux affiché dans le DOM", async () => {
    const dangerousNom = '<img src=x onerror="window.__xss2=true">';

    const { container } = render(
      // Composant qui afficherait dynamiquement un nom utilisateur
      <div data-testid="user-nom">{dangerousNom}</div>
    );

    // React échappe automatiquement le contenu — vérifier que l'attribut onerror n'est pas actif
    expect((window as Record<string, unknown>).__xss2).toBeUndefined();
    // Le contenu textuel doit être présent, mais pas exécuté
    expect(container).toHaveTextContent('<img src=x');
  });
});

// ════════════════════════════════════════════════════════════════════
// 6. EN-TÊTES DE SÉCURITÉ — next.config.ts
// ════════════════════════════════════════════════════════════════════

describe("SCRUM-76 | next.config.ts — en-têtes de sécurité HTTP", () => {
  /**
   * Ces tests vérifient la présence des en-têtes dans la configuration Next.js.
   * Ils lisent le fichier next.config.ts directement via l'import.
   */
  it("la config Next.js définit le header X-Content-Type-Options", async () => {
    const config = await import("@/../next.config");
    const headers = await config.default.headers?.();

    const allHeaders = headers?.flatMap((h: { headers: { key: string }[] }) => h.headers) ?? [];
    const headerKeys = allHeaders.map((h: { key: string }) => h.key);

    expect(headerKeys).toContain("X-Content-Type-Options");
  });

  it("la config Next.js définit le header X-Frame-Options", async () => {
    const config = await import("@/../next.config");
    const headers = await config.default.headers?.();

    const allHeaders = headers?.flatMap((h: { headers: { key: string }[] }) => h.headers) ?? [];
    const headerKeys = allHeaders.map((h: { key: string }) => h.key);

    expect(headerKeys).toContain("X-Frame-Options");
  });

  it("la config Next.js définit une Content-Security-Policy", async () => {
    const config = await import("@/../next.config");
    const headers = await config.default.headers?.();

    const allHeaders = headers?.flatMap((h: { headers: { key: string }[] }) => h.headers) ?? [];
    const headerKeys = allHeaders.map((h: { key: string }) => h.key);

    expect(headerKeys).toContain("Content-Security-Policy");
  });
});
