<?php

namespace App\Entity;

use App\Repository\WantedRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WantedRepository::class)]
class Wanted
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $title = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $finishedAt = null;

    #[ORM\Column]
    private ?int $Prime = null;

    #[ORM\Column]
    private ?int $fine = null;

    #[ORM\Column]
    private ?int $prisonTime = null;

    #[ORM\Column]
    private ?bool $isInProgress = null;

    /**
     * @var Collection<int, WantedPeople>
     */
    #[ORM\OneToMany(targetEntity: WantedPeople::class, mappedBy: 'wanted')]
    private Collection $wantedPeople;

    public function __construct()
    {
        $this->wantedPeople = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getFinishedAt(): ?\DateTimeImmutable
    {
        return $this->finishedAt;
    }

    public function setFinishedAt(\DateTimeImmutable $finishedAt): static
    {
        $this->finishedAt = $finishedAt;

        return $this;
    }

    public function getPrime(): ?int
    {
        return $this->Prime;
    }

    public function setPrime(int $Prime): static
    {
        $this->Prime = $Prime;

        return $this;
    }

    public function getFine(): ?int
    {
        return $this->fine;
    }

    public function setFine(int $fine): static
    {
        $this->fine = $fine;

        return $this;
    }

    public function getPrisonTime(): ?int
    {
        return $this->prisonTime;
    }

    public function setPrisonTime(int $prisonTime): static
    {
        $this->prisonTime = $prisonTime;

        return $this;
    }

    public function isInProgress(): ?bool
    {
        return $this->isInProgress;
    }

    public function setIsInProgress(bool $isInProgress): static
    {
        $this->isInProgress = $isInProgress;

        return $this;
    }

    /**
     * @return Collection<int, WantedPeople>
     */
    public function getWantedPeople(): Collection
    {
        return $this->wantedPeople;
    }

    public function addWantedPerson(WantedPeople $wantedPerson): static
    {
        if (!$this->wantedPeople->contains($wantedPerson)) {
            $this->wantedPeople->add($wantedPerson);
            $wantedPerson->setWanted($this);
        }

        return $this;
    }

    public function removeWantedPerson(WantedPeople $wantedPerson): static
    {
        if ($this->wantedPeople->removeElement($wantedPerson)) {
            // set the owning side to null (unless already changed)
            if ($wantedPerson->getWanted() === $this) {
                $wantedPerson->setWanted(null);
            }
        }

        return $this;
    }
}
