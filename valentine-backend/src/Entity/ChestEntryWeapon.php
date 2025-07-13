<?php

namespace App\Entity;

use App\Repository\ChestEntryWeaponRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChestEntryWeaponRepository::class)]
class ChestEntryWeapon
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $entryAt = null;

    #[ORM\ManyToOne(inversedBy: 'chestEntryWeapons')]
    private ?ChestWeapon $chest = null;

    #[ORM\ManyToOne(inversedBy: 'chestEntryWeapons')]
    private ?Weapon $weapon = null;

    #[ORM\Column(length: 255)]
    private ?string $reference = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEntryAt(): ?\DateTimeImmutable
    {
        return $this->entryAt;
    }

    public function setEntryAt(\DateTimeImmutable $entryAt): static
    {
        $this->entryAt = $entryAt;

        return $this;
    }

    public function getChest(): ?ChestWeapon
    {
        return $this->chest;
    }

    public function setChest(?ChestWeapon $chest): static
    {
        $this->chest = $chest;

        return $this;
    }

    public function getWeapon(): ?Weapon
    {
        return $this->weapon;
    }

    public function setWeapon(?Weapon $weapon): static
    {
        $this->weapon = $weapon;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): static
    {
        $this->reference = $reference;

        return $this;
    }
}
