<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\EdtRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EdtRepository::class)]
#[ApiResource]
class Edt
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $qrcode = null;

    #[ORM\ManyToOne(inversedBy: 'id_edt')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Journee $id_journee = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQrcode(): ?string
    {
        return $this->qrcode;
    }

    public function setQrcode(string $qrcode): static
    {
        $this->qrcode = $qrcode;

        return $this;
    }

    public function getIdJournee(): ?Journee
    {
        return $this->id_journee;
    }

    public function setIdJournee(?Journee $id_journee): static
    {
        $this->id_journee = $id_journee;

        return $this;
    }
}
