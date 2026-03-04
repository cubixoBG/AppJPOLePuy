<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\JourneeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JourneeRepository::class)]
#[ApiResource]
class Journee
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'id_journee')]
    private Collection $id_user;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTime $date = null;

    /**
     * @var Collection<int, Edt>
     */
    #[ORM\OneToMany(targetEntity: Edt::class, mappedBy: 'id_journee', orphanRemoval: true)]
    private Collection $id_edt;

    public function __construct()
    {
        $this->id_user = new ArrayCollection();
        $this->id_edt = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, User>
     */
    public function getIdUser(): Collection
    {
        return $this->id_user;
    }

    public function addIdUser(User $idUser): static
    {
        if (!$this->id_user->contains($idUser)) {
            $this->id_user->add($idUser);
            $idUser->setIdJournee($this);
        }

        return $this;
    }

    public function removeIdUser(User $idUser): static
    {
        if ($this->id_user->removeElement($idUser)) {
            // set the owning side to null (unless already changed)
            if ($idUser->getIdJournee() === $this) {
                $idUser->setIdJournee(null);
            }
        }

        return $this;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): static
    {
        $this->date = $date;

        return $this;
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
            $idEdt->setIdJournee($this);
        }

        return $this;
    }

    public function removeIdEdt(Edt $idEdt): static
    {
        if ($this->id_edt->removeElement($idEdt)) {
            // set the owning side to null (unless already changed)
            if ($idEdt->getIdJournee() === $this) {
                $idEdt->setIdJournee(null);
            }
        }

        return $this;
    }
}
