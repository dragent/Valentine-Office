<?php

namespace App\Entity;

use App\Repository\ChestRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChestRepository::class)]
class Chest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\Column]
    private ?bool $isLegal = null;

    /**
     * @var Collection<int, ChestProduct>
     */
    #[ORM\OneToMany(targetEntity: ChestProduct::class, mappedBy: 'chest')]
    private Collection $chestProducts;

    /**
     * @var Collection<int, ChestWeapon>
     */
    #[ORM\OneToMany(targetEntity: ChestWeapon::class, mappedBy: 'chest')]
    private Collection $chestWeapons;

    public function __construct()
    {
        $this->chestProducts = new ArrayCollection();
        $this->chestWeapons = new ArrayCollection();
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

    public function isLegal(): ?bool
    {
        return $this->isLegal;
    }

    public function setIsLegal(bool $isLegal): static
    {
        $this->isLegal = $isLegal;

        return $this;
    }

    /**
     * @return Collection<int, ChestProduct>
     */
    public function getChestProducts(): Collection
    {
        return $this->chestProducts;
    }

    public function addChestProduct(ChestProduct $chestProduct): static
    {
        if (!$this->chestProducts->contains($chestProduct)) {
            $this->chestProducts->add($chestProduct);
            $chestProduct->setChest($this);
        }

        return $this;
    }

    public function removeChestProduct(ChestProduct $chestProduct): static
    {
        if ($this->chestProducts->removeElement($chestProduct)) {
            // set the owning side to null (unless already changed)
            if ($chestProduct->getChest() === $this) {
                $chestProduct->setChest(null);
            }
        }

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
            $chestWeapon->setChest($this);
        }

        return $this;
    }

    public function removeChestWeapon(ChestWeapon $chestWeapon): static
    {
        if ($this->chestWeapons->removeElement($chestWeapon)) {
            // set the owning side to null (unless already changed)
            if ($chestWeapon->getChest() === $this) {
                $chestWeapon->setChest(null);
            }
        }

        return $this;
    }
}
