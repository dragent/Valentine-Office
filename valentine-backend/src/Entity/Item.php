<?php

namespace App\Entity;

use App\Repository\ItemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ItemRepository::class)]
class Item
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\Column]
    private ?bool $isLegal = null;

    #[ORM\Column]
    private ?int $price = null;

    /**
     * @var Collection<int, ChestProduct>
     */
    #[ORM\OneToMany(targetEntity: ChestProduct::class, mappedBy: 'item')]
    private Collection $chestProducts;

    public function __construct()
    {
        $this->chestProducts = new ArrayCollection();
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

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): static
    {
        $this->price = $price;

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
            $chestProduct->setItem($this);
        }

        return $this;
    }

    public function removeChestProduct(ChestProduct $chestProduct): static
    {
        if ($this->chestProducts->removeElement($chestProduct)) {
            // set the owning side to null (unless already changed)
            if ($chestProduct->getItem() === $this) {
                $chestProduct->setItem(null);
            }
        }

        return $this;
    }
}
