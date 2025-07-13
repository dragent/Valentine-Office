<?php

namespace App\Entity;

use App\Repository\ChestProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChestProductRepository::class)]
class ChestProduct
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'chestProducts')]
    private ?Item $item = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\ManyToOne(inversedBy: 'chestProducts')]
    private ?Chest $chest = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $categpry = null;

    /**
     * @var Collection<int, ChestEntryItem>
     */
    #[ORM\OneToMany(targetEntity: ChestEntryItem::class, mappedBy: 'chestItem')]
    private Collection $chestEntryItems;

    /**
     * @var Collection<int, DestructionItem>
     */
    #[ORM\OneToMany(targetEntity: DestructionItem::class, mappedBy: 'item')]
    private Collection $destructionItems;

    public function __construct()
    {
        $this->chestEntryItems = new ArrayCollection();
        $this->destructionItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getItem(): ?Item
    {
        return $this->item;
    }

    public function setItem(?Item $item): static
    {
        $this->item = $item;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

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

    public function getCategpry(): ?string
    {
        return $this->categpry;
    }

    public function setCategpry(?string $categpry): static
    {
        $this->categpry = $categpry;

        return $this;
    }

    /**
     * @return Collection<int, ChestEntryItem>
     */
    public function getChestEntryItems(): Collection
    {
        return $this->chestEntryItems;
    }

    public function addChestEntryItem(ChestEntryItem $chestEntryItem): static
    {
        if (!$this->chestEntryItems->contains($chestEntryItem)) {
            $this->chestEntryItems->add($chestEntryItem);
            $chestEntryItem->setChestItem($this);
        }

        return $this;
    }

    public function removeChestEntryItem(ChestEntryItem $chestEntryItem): static
    {
        if ($this->chestEntryItems->removeElement($chestEntryItem)) {
            // set the owning side to null (unless already changed)
            if ($chestEntryItem->getChestItem() === $this) {
                $chestEntryItem->setChestItem(null);
            }
        }

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
            $destructionItem->setItem($this);
        }

        return $this;
    }

    public function removeDestructionItem(DestructionItem $destructionItem): static
    {
        if ($this->destructionItems->removeElement($destructionItem)) {
            // set the owning side to null (unless already changed)
            if ($destructionItem->getItem() === $this) {
                $destructionItem->setItem(null);
            }
        }

        return $this;
    }
}
