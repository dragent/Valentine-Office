<?php

namespace App\Entity;

use App\Repository\WantedPeopleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WantedPeopleRepository::class)]
class WantedPeople
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $Criminal = null;

    #[ORM\Column]
    private ?bool $isCaptured = null;

    #[ORM\ManyToOne(inversedBy: 'wantedPeople')]
    private ?Wanted $wanted = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCriminal(): ?string
    {
        return $this->Criminal;
    }

    public function setCriminal(string $Criminal): static
    {
        $this->Criminal = $Criminal;

        return $this;
    }

    public function isCaptured(): ?bool
    {
        return $this->isCaptured;
    }

    public function setIsCaptured(bool $isCaptured): static
    {
        $this->isCaptured = $isCaptured;

        return $this;
    }

    public function getWanted(): ?Wanted
    {
        return $this->wanted;
    }

    public function setWanted(?Wanted $wanted): static
    {
        $this->wanted = $wanted;

        return $this;
    }
}
