<?php

namespace App\Entity;

use App\Repository\InternalReportRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InternalReportRepository::class)]
class InternalReport
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $link = null;

    #[ORM\ManyToOne(inversedBy: 'internalReports')]
    private ?User $sheriff = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(string $link): static
    {
        $this->link = $link;

        return $this;
    }

    public function getSheriff(): ?User
    {
        return $this->sheriff;
    }

    public function setSheriff(?User $sheriff): static
    {
        $this->sheriff = $sheriff;

        return $this;
    }
}
