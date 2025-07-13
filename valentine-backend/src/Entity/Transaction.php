<?php

namespace App\Entity;

use App\Repository\TransactionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TransactionRepository::class)]
class Transaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $transactAt = null;

    #[ORM\Column]
    private ?bool $isEntry = null;

    #[ORM\Column]
    private ?int $money = null;

    #[ORM\ManyToOne(inversedBy: 'transactions')]
    private ?Comptability $comtability = null;

    #[ORM\ManyToOne(inversedBy: 'transactions')]
    private ?user $sheriff = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTransactAt(): ?\DateTimeImmutable
    {
        return $this->transactAt;
    }

    public function setTransactAt(\DateTimeImmutable $transactAt): static
    {
        $this->transactAt = $transactAt;

        return $this;
    }

    public function isEntry(): ?bool
    {
        return $this->isEntry;
    }

    public function setIsEntry(bool $isEntry): static
    {
        $this->isEntry = $isEntry;

        return $this;
    }

    public function getMoney(): ?int
    {
        return $this->money;
    }

    public function setMoney(int $money): static
    {
        $this->money = $money;

        return $this;
    }

    public function getComtability(): ?Comptability
    {
        return $this->comtability;
    }

    public function setComtability(?Comptability $comtability): static
    {
        $this->comtability = $comtability;

        return $this;
    }

    public function getSheriff(): ?user
    {
        return $this->sheriff;
    }

    public function setSheriff(?user $sheriff): static
    {
        $this->sheriff = $sheriff;

        return $this;
    }
}
