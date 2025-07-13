<?php

namespace App\Entity;

use App\Repository\DestructionWeaponRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DestructionWeaponRepository::class)]
class DestructionWeapon
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'destructionWeapons')]
    private ?ChestWeapon $weapon = null;

    #[ORM\ManyToOne(inversedBy: 'destructionWeapons')]
    private ?Destruction $Destruction = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWeapon(): ?ChestWeapon
    {
        return $this->weapon;
    }

    public function setWeapon(?ChestWeapon $weapon): static
    {
        $this->weapon = $weapon;

        return $this;
    }

    public function getDestruction(): ?Destruction
    {
        return $this->Destruction;
    }

    public function setDestruction(?Destruction $Destruction): static
    {
        $this->Destruction = $Destruction;

        return $this;
    }
}
