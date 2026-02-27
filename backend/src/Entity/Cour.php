<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\CourRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CourRepository::class)]
#[ApiResource]
class Cour
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, Edt>
     */
    #[ORM\ManyToMany(targetEntity: Edt::class, inversedBy: 'duree')]
    private Collection $id_edt;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $duree = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $annee = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $enseignant = null;

    public function __construct()
    {
        $this->id_edt = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Edt>
     */
    public function getIdEdt(): Collection
    {
        return $this->id_edt;
    }

    public function addIdEdt(Edt $idEdt): static
    {
        if (!$this->id_edt->contains($idEdt)) {
            $this->id_edt->add($idEdt);
        }

        return $this;
    }

    public function removeIdEdt(Edt $idEdt): static
    {
        $this->id_edt->removeElement($idEdt);

        return $this;
    }

    public function getDuree(): ?string
    {
        return $this->duree;
    }

    public function setDuree(?string $duree): static
    {
        $this->duree = $duree;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getAnnee(): ?string
    {
        return $this->annee;
    }

    public function setAnnee(?string $annee): static
    {
        $this->annee = $annee;

        return $this;
    }

    public function getEnseignant(): ?string
    {
        return $this->enseignant;
    }

    public function setEnseignant(?string $enseignant): static
    {
        $this->enseignant = $enseignant;

        return $this;
    }
}
