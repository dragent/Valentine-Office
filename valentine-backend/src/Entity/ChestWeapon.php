<?php

namespace App\Entity;

use App\Repository\ChestWeaponRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChestWeaponRepository::class)]
class ChestWeapon
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'chestWeapons')]
    private ?Weapon $weapon = null;

    #[ORM\ManyToOne(inversedBy: 'chestWeapons')]
    private ?Chest $chest = null;

    #[ORM\Column(length: 16, nullable: true)]
    private ?string $reference = null;

    #[ORM\Column(length: 100)]
    private ?string $owner = null;

    /**
     * @var Collection<int, ChestEntryWeapon>
     */
    #[ORM\OneToMany(targetEntity: ChestEntryWeapon::class, mappedBy: 'chest')]
    private Collection $chestEntryWeapons;

    /**
     * @var Collection<int, DestructionWeapon>
     */
    #[ORM\OneToMany(targetEntity: DestructionWeapon::class, mappedBy: 'weapon')]
    private Collection $destructionWeapons;

    public function __construct()
    {
        $this->chestEntryWeapons = new ArrayCollection();
        $this->destructionWeapons = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getChest(): ?Chest
    {
        return $this->chest;
    }

    public function setChest(?Chest $chest): static
    {
        $this->chest = $chest;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): static
    {
        $this->reference = $reference;

        return $this;
    }

    public function getOwner(): ?string
    {
        return $this->owner;
    }

    public function setOwner(string $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection<int, ChestEntryWeapon>
     */
    public function getChestEntryWeapons(): Collection
    {
        return $this->chestEntryWeapons;
    }

    public function addChestEntryWeapon(ChestEntryWeapon $chestEntryWeapon): static
    {
        if (!$this->chestEntryWeapons->contains($chestEntryWeapon)) {
            $this->chestEntryWeapons->add($chestEntryWeapon);
            $chestEntryWeapon->setChest($this);
        }

        return $this;
    }

    public function removeChestEntryWeapon(ChestEntryWeapon $chestEntryWeapon): static
    {
        if ($this->chestEntryWeapons->removeElement($chestEntryWeapon)) {
            // set the owning side to null (unless already changed)
            if ($chestEntryWeapon->getChest() === $this) {
                $chestEntryWeapon->setChest(null);
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
            $destructionWeapon->setWeapon($this);
        }

        return $this;
    }

    public function removeDestructionWeapon(DestructionWeapon $destructionWeapon): static
    {
        if ($this->destructionWeapons->removeElement($destructionWeapon)) {
            // set the owning side to null (unless already changed)
            if ($destructionWeapon->getWeapon() === $this) {
                $destructionWeapon->setWeapon(null);
            }
        }

        return $this;
    }
}
