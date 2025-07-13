<?php

namespace App\Entity;

use App\Repository\FormationCheckRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FormationCheckRepository::class)]
class FormationCheck
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'formationChecks')]
    private ?User $sheriff = null;

    #[ORM\ManyToOne(inversedBy: 'formationChecks')]
    private ?Formation $formation = null;

    #[ORM\Column]
    private ?bool $isValided = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getFormation(): ?Formation
    {
        return $this->formation;
    }

    public function setFormation(?Formation $formation): static
    {
        $this->formation = $formation;

        return $this;
    }

    public function isValided(): ?bool
    {
        return $this->isValided;
    }

    public function setIsValided(bool $isValided): static
    {
        $this->isValided = $isValided;

        return $this;
    }
}
