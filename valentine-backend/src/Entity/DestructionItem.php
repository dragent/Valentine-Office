<?php

namespace App\Entity;

use App\Repository\DestructionItemRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DestructionItemRepository::class)]
class DestructionItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'destructionItems')]
    private ?ChestProduct $item = null;

    #[ORM\ManyToOne(inversedBy: 'destructionItems')]
    private ?Destruction $destruction = null;

    #[ORM\Column]
    private ?int $quantity = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getItem(): ?ChestProduct
    {
        return $this->item;
    }

    public function setItem(?ChestProduct $item): static
    {
        $this->item = $item;

        return $this;
    }

    public function getDestruction(): ?Destruction
    {
        return $this->destruction;
    }

    public function setDestruction(?Destruction $destruction): static
    {
        $this->destruction = $destruction;

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
