<?php

namespace App\Entity;

use App\Repository\DestructionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DestructionRepository::class)]
class Destruction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $realisedAt = null;

    #[ORM\Column]
    private ?int $state = null;

    /**
     * @var Collection<int, DestructionItem>
     */
    #[ORM\OneToMany(targetEntity: DestructionItem::class, mappedBy: 'destruction')]
    private Collection $destructionItems;

    /**
     * @var Collection<int, DestructionWeapon>
     */
    #[ORM\OneToMany(targetEntity: DestructionWeapon::class, mappedBy: 'Destruction')]
    private Collection $destructionWeapons;

    public function __construct()
    {
        $this->destructionItems = new ArrayCollection();
        $this->destructionWeapons = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRealisedAt(): ?\DateTimeImmutable
    {
        return $this->realisedAt;
    }

    public function setRealisedAt(\DateTimeImmutable $realisedAt): static
    {
        $this->realisedAt = $realisedAt;

        return $this;
    }

    public function getState(): ?int
    {
        return $this->state;
    }

    public function setState(int $state): static
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @return Collection<int, DestructionItem>
     */
    public function getDestructionItems(): Collection
    {
        return $this->destructionItems;
    }

    public function addDestructionItem(DestructionItem $destructionItem): static
    {
        if (!$this->destructionItems->contains($destructionItem)) {
            $this->destructionItems->add($destructionItem);
            $destructionItem->setDestruction($this);
        }

        return $this;
    }

    public function removeDestructionItem(DestructionItem $destructionItem): static
    {
        if ($this->destructionItems->removeElement($destructionItem)) {
            // set the owning side to null (unless already changed)
            if ($destructionItem->getDestruction() === $this) {
                $destructionItem->setDestruction(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DestructionWeapon>
     */
    public function getDestructionWeapons(): Collection
    {
        return $this->destructionWeapons;
    }

    public function addDestructionWeapon(DestructionWeapon $destructionWeapon): static
    {
        if (!$this->destructionWeapons->contains($destructionWeapon)) {
            $this->destructionWeapons->add($destructionWeapon);
            $destructionWeapon->setDestruction($this);
        }

        return $this;
    }

    public function removeDestructionWeapon(DestructionWeapon $destructionWeapon): static
    {
        if ($this->destructionWeapons->removeElement($destructionWeapon)) {
            // set the owning side to null (unless already changed)
            if ($destructionWeapon->getDestruction() === $this) {
                $destructionWeapon->setDestruction(null);
            }
        }

        return $this;
    }
}
