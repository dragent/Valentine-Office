<?php

namespace App\Entity;

use App\Repository\ChestEntryItemRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChestEntryItemRepository::class)]
class ChestEntryItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $entryAt = null;

    #[ORM\ManyToOne(inversedBy: 'chestEntryItems')]
    private ?ChestProduct $chestItem = null;

    #[ORM\Column]
    private ?int $quantity = null;

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

    public function getChestItem(): ?ChestProduct
    {
        return $this->chestItem;
    }

    public function setChestItem(?ChestProduct $chestItem): static
    {
        $this->chestItem = $chestItem;

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
}
