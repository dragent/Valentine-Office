<?php

namespace App\Entity;

use App\Repository\AmendeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AmendeRepository::class)]
class Amende
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $isGivenAt = null;

    #[ORM\Column(length: 255)]
    private ?string $Criminal = null;

    #[ORM\Column(length: 10)]
    private ?string $Telegramme = null;

    #[ORM\Column]
    private ?int $amount = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $isLimitAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $isRemindedAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $isRemindLimitAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $limitAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $reÃmindAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $remindLimitAt = null;

    #[ORM\Column]
    private ?bool $isJudgeContacted = null;

    #[ORM\Column]
    private ?bool $isWanted = null;

    #[ORM\Column]
    private ?bool $isPaid = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIsGivenAt(): ?\DateTimeImmutable
    {
        return $this->isGivenAt;
    }

    public function setIsGivenAt(\DateTimeImmutable $isGivenAt): static
    {
        $this->isGivenAt = $isGivenAt;

        return $this;
    }

    public function getCriminal(): ?string
    {
        return $this->Criminal;
    }

    public function setCriminal(string $Criminal): static
    {
        $this->Criminal = $Criminal;

        return $this;
    }

    public function getTelegramme(): ?string
    {
        return $this->Telegramme;
    }

    public function setTelegramme(string $Telegramme): static
    {
        $this->Telegramme = $Telegramme;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getIsLimitAt(): ?\DateTimeImmutable
    {
        return $this->isLimitAt;
    }

    public function setIsLimitAt(\DateTimeImmutable $isLimitAt): static
    {
        $this->isLimitAt = $isLimitAt;

        return $this;
    }

    public function getIsRemindedAt(): ?\DateTimeImmutable
    {
        return $this->isRemindedAt;
    }

    public function setIsRemindedAt(\DateTimeImmutable $isRemindedAt): static
    {
        $this->isRemindedAt = $isRemindedAt;

        return $this;
    }

    public function getIsRemindLimitAt(): ?\DateTimeImmutable
    {
        return $this->isRemindLimitAt;
    }

    public function setIsRemindLimitAt(\DateTimeImmutable $isRemindLimitAt): static
    {
        $this->isRemindLimitAt = $isRemindLimitAt;

        return $this;
    }

    public function getLimitAt(): ?\DateTimeImmutable
    {
        return $this->limitAt;
    }

    public function setLimitAt(\DateTimeImmutable $limitAt): static
    {
        $this->limitAt = $limitAt;

        return $this;
    }

    public function getReÃmindAt(): ?\DateTimeImmutable
    {
        return $this->reÃmindAt;
    }

    public function setReÃmindAt(\DateTimeImmutable $reÃmindAt): static
    {
        $this->reÃmindAt = $reÃmindAt;

        return $this;
    }

    public function getRemindLimitAt(): ?\DateTimeImmutable
    {
        return $this->remindLimitAt;
    }

    public function setRemindLimitAt(\DateTimeImmutable $remindLimitAt): static
    {
        $this->remindLimitAt = $remindLimitAt;

        return $this;
    }

    public function isJudgeContacted(): ?bool
    {
        return $this->isJudgeContacted;
    }

    public function setIsJudgeContacted(bool $isJudgeContacted): static
    {
        $this->isJudgeContacted = $isJudgeContacted;

        return $this;
    }

    public function isWanted(): ?bool
    {
        return $this->isWanted;
    }

    public function setIsWanted(bool $isWanted): static
    {
        $this->isWanted = $isWanted;

        return $this;
    }

    public function isPaid(): ?bool
    {
        return $this->isPaid;
    }

    public function setIsPaid(bool $isPaid): static
    {
        $this->isPaid = $isPaid;

        return $this;
    }
}
