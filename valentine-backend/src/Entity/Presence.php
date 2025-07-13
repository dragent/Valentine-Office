<?php

namespace App\Entity;

use App\Repository\PresenceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PresenceRepository::class)]
class Presence
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'presences')]
    private ?user $sheriff = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $signedAt = null;

    #[ORM\Column]
    private ?bool $isDay = null;

    #[ORM\Column]
    private ?bool $isNight = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSignedAt(): ?\DateTimeImmutable
    {
        return $this->signedAt;
    }

    public function setSignedAt(\DateTimeImmutable $signedAt): static
    {
        $this->signedAt = $signedAt;

        return $this;
    }

    public function isDay(): ?bool
    {
        return $this->isDay;
    }

    public function setIsDay(bool $isDay): static
    {
        $this->isDay = $isDay;

        return $this;
    }

    public function isNight(): ?bool
    {
        return $this->isNight;
    }

    public function setIsNight(bool $isNight): static
    {
        $this->isNight = $isNight;

        return $this;
    }
}
