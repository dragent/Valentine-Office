<?php

namespace App\Entity;

use App\Repository\FormationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FormationRepository::class)]
class Formation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    /**
     * @var Collection<int, FormationCheck>
     */
    #[ORM\OneToMany(targetEntity: FormationCheck::class, mappedBy: 'formation')]
    private Collection $formationChecks;

    public function __construct()
    {
        $this->formationChecks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return Collection<int, FormationCheck>
     */
    public function getFormationChecks(): Collection
    {
        return $this->formationChecks;
    }

    public function addFormationCheck(FormationCheck $formationCheck): static
    {
        if (!$this->formationChecks->contains($formationCheck)) {
            $this->formationChecks->add($formationCheck);
            $formationCheck->setFormation($this);
        }

        return $this;
    }

    public function removeFormationCheck(FormationCheck $formationCheck): static
    {
        if ($this->formationChecks->removeElement($formationCheck)) {
            // set the owning side to null (unless already changed)
            if ($formationCheck->getFormation() === $this) {
                $formationCheck->setFormation(null);
            }
        }

        return $this;
    }
}
