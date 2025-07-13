<?php

namespace App\Entity;

use App\Repository\WeaponRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WeaponRepository::class)]
class Weapon
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $price = null;

    #[ORM\Column(length: 50)]
    private ?string $type = null;

    /**
     * @var Collection<int, ChestWeapon>
     */
    #[ORM\OneToMany(targetEntity: ChestWeapon::class, mappedBy: 'weapon')]
    private Collection $chestWeapons;

    /**
     * @var Collection<int, ChestEntryWeapon>
     */
    #[ORM\OneToMany(targetEntity: ChestEntryWeapon::class, mappedBy: 'weapon')]
    private Collection $chestEntryWeapons;

    public function __construct()
    {
        $this->chestWeapons = new ArrayCollection();
        $this->chestEntryWeapons = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection<int, ChestWeapon>
     */
    public function getChestWeapons(): Collection
    {
        return $this->chestWeapons;
    }

    public function addChestWeapon(ChestWeapon $chestWeapon): static
    {
        if (!$this->chestWeapons->contains($chestWeapon)) {
            $this->chestWeapons->add($chestWeapon);
            $chestWeapon->setWeapon($this);
        }

        return $this;
    }

    public function removeChestWeapon(ChestWeapon $chestWeapon): static
    {
        if ($this->chestWeapons->removeElement($chestWeapon)) {
            // set the owning side to null (unless already changed)
            if ($chestWeapon->getWeapon() === $this) {
                $chestWeapon->setWeapon(null);
            }
        }

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
            $chestEntryWeapon->setWeapon($this);
        }

        return $this;
    }

    public function removeChestEntryWeapon(ChestEntryWeapon $chestEntryWeapon): static
    {
        if ($this->chestEntryWeapons->removeElement($chestEntryWeapon)) {
            // set the owning side to null (unless already changed)
            if ($chestEntryWeapon->getWeapon() === $this) {
                $chestEntryWeapon->setWeapon(null);
            }
        }

        return $this;
    }
}
