<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'Cet email est déjà utilisé.')]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    // ── Champs obligatoires ───────────────────────────────────────────────

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'Le nom ne peut pas être vide.')]
    #[Assert\Length(max: 100)]
    private string $nom = '';

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'Le prénom ne peut pas être vide.')]
    #[Assert\Length(max: 100)]
    private string $prenom = '';

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank(message: 'L\'email ne peut pas être vide.')]
    #[Assert\Email(message: 'L\'email {{ value }} n\'est pas valide.')]
    private string $email = '';

    #[ORM\Column(length: 20, nullable: true)]
    #[Assert\Regex(
        pattern: '/^[0-9]{10}$/',
        message: 'Le numéro de téléphone doit contenir 10 chiffres.'
    )]
    private ?string $tel = null;

    #[ORM\Column(length: 150)]
    #[Assert\NotBlank(message: 'L\'établissement ne peut pas être vide.')]
    private string $etablissement = '';

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $departement = null;

    // ── Mot de passe ──────────────────────────────────────────────────────

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Le mot de passe ne peut pas être vide.')]
    #[Assert\Length(
        min: 8,
        minMessage: 'Le mot de passe doit contenir au moins {{ limit }} caractères.'
    )]
    private string $mdp = '';

    // ── Rôles Symfony ─────────────────────────────────────────────────────

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    // ── Type utilisateur ─────────────────────────────────────────────────

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank(message: 'Le type ne peut pas être vide.')]
    #[Assert\Choice(
        choices: ['visiteur', 'ambassadeur', 'admin'],
        message: 'Le type doit être visiteur, ambassadeur ou admin.'
    )]
    private string $type = 'visiteur';

    // ── Statut étudiant ───────────────────────────────────────────────────

    #[ORM\Column(length: 30, nullable: true)]
    #[Assert\Choice(
        choices: ['lyceen', 'etudiant', 'professionnel', 'autre'],
        message: 'Le statut étudiant n\'est pas valide.'
    )]
    private ?string $statutEtu = null;

    // ── Heure d'arrivée ───────────────────────────────────────────────────

    #[ORM\Column(type: 'time', nullable: true)]
    private ?\DateTimeInterface $heureArriver = null;

    // =========================================================================
    // Getters / Setters
    // =========================================================================

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(?string $tel): static
    {
        $this->tel = $tel;
        return $this;
    }

    public function getEtablissement(): string
    {
        return $this->etablissement;
    }

    public function setEtablissement(string $etablissement): static
    {
        $this->etablissement = $etablissement;
        return $this;
    }

    public function getDepartement(): ?string
    {
        return $this->departement;
    }

    public function setDepartement(?string $departement): static
    {
        $this->departement = $departement;
        return $this;
    }

    public function getMdp(): string
    {
        return $this->mdp;
    }

    public function setMdp(string $mdp): static
    {
        $this->mdp = $mdp;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getStatutEtu(): ?string
    {
        return $this->statutEtu;
    }

    public function setStatutEtu(?string $statutEtu): static
    {
        $this->statutEtu = $statutEtu;
        return $this;
    }

    public function getHeureArriver(): ?\DateTimeInterface
    {
        return $this->heureArriver;
    }

    public function setHeureArriver(?\DateTimeInterface $heureArriver): static
    {
        $this->heureArriver = $heureArriver;
        return $this;
    }

    // ── UserInterface ─────────────────────────────────────────────────────

    public function getRoles(): array
    {
        $roles = $this->roles;
        // Garantit qu'il y a toujours au moins ROLE_USER
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->mdp;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function eraseCredentials(): void
    {
        // Vide les données sensibles temporaires si besoin
    }

    // ── Logique métier ────────────────────────────────────────────────────

    /**
     * Seul un visiteur peut s'inscrire à une journée d'immersion.
     * (SCRUM-75 - règle métier)
     */
    public function canInscribeImmersion(): bool
    {
        return $this->type === 'visiteur';
    }
}
