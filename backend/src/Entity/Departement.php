<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\DepartementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DepartementRepository::class)]
#[ApiResource]
class Departement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $logo = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nom_responsable = null;

    /**
     * @var Collection<int, Indice>
     */
    #[ORM\OneToMany(targetEntity: Indice::class, mappedBy: 'departement', orphanRemoval: true)]
    private Collection $indices;

    #[ORM\OneToMany(targetEntity: Contact::class, mappedBy: 'departement', orphanRemoval: true)]
    private Collection $contacts;

    /**
     * @var Collection<int, Journee>
     */
    #[ORM\OneToMany(targetEntity: Journee::class, mappedBy: 'departement', orphanRemoval: true)]
    private Collection $journees;

    public function __construct()
    {
        $this->indices = new ArrayCollection();
        $this->journees = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): static
    {
        $this->logo = $logo;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getNomResponsable(): ?string
    {
        return $this->nom_responsable;
    }

    public function setNomResponsable(?string $nom_responsable): static
    {
        $this->nom_responsable = $nom_responsable;

        return $this;
    }

    /**
     * @return Collection<int, Indice>
     */
    public function getIndices(): Collection
    {
        return $this->indices;
    }

    public function addIndex(Indice $index): static
    {
        if (!$this->indices->contains($index)) {
            $this->indices->add($index);
            $index->setDepartement($this);
        }

        return $this;
    }

    public function removeIndex(Indice $index): static
    {
        if ($this->indices->removeElement($index)) {
            // set the owning side to null (unless already changed)
            if ($index->getDepartement() === $this) {
                $index->setDepartement(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Contact>
     */
    public function getContacts(): Collection
    {
        return $this->contacts;
    }

    public function addContact(Contact $contact): static
    {
        if (!$this->contacts->contains($contact)) {
            $this->contacts->add($contact);
            $contact->setDepartement($this);
        }

        return $this;
    }

    public function removeContact(Contact $contact): static
    {
        if ($this->contacts->removeElement($contact)) {
            // set the owning side to null (unless already changed)
            if ($contact->getDepartement() === $this) {
                $contact->setDepartement(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Journee>
     */
    public function getJournees(): Collection
    {
        return $this->journees;
    }

    public function addJournee(Journee $journee): static
    {
        if (!$this->journees->contains($journee)) {
            $this->journees->add($journee);
            $journee->setDepartement($this);
        }

        return $this;
    }

    public function removeJournee(Journee $journee): static
    {
        if ($this->journees->removeElement($journee)) {
            // set the owning side to null (unless already changed)
            if ($journee->getDepartement() === $this) {
                $journee->setDepartement(null);
            }
        }

        return $this;
    }
}
