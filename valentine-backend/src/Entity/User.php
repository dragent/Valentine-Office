<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column(length: 50)]
    private ?string $discordId = null;

    #[ORM\Column(length: 100)]
    private ?string $avatarUrl = null;

    #[ORM\Column(length: 50)]
    private ?string $username = null;

    /**
     * @var Collection<int, Transaction>
     */
    #[ORM\OneToMany(targetEntity: Transaction::class, mappedBy: 'sheriff')]
    private Collection $transactions;

    /**
     * @var Collection<int, InternalReport>
     */
    #[ORM\OneToMany(targetEntity: InternalReport::class, mappedBy: 'sheriff')]
    private Collection $internalReports;

    /**
     * @var Collection<int, Presence>
     */
    #[ORM\OneToMany(targetEntity: Presence::class, mappedBy: 'sheriff')]
    private Collection $presences;

    /**
     * @var Collection<int, Folder>
     */
    #[ORM\OneToMany(targetEntity: Folder::class, mappedBy: 'sheriffInCharge')]
    private Collection $folders;

    /**
     * @var Collection<int, FormationCheck>
     */
    #[ORM\OneToMany(targetEntity: FormationCheck::class, mappedBy: 'sheriff')]
    private Collection $formationChecks;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
        $this->internalReports = new ArrayCollection();
        $this->presences = new ArrayCollection();
        $this->folders = new ArrayCollection();
        $this->formationChecks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';
        $roles = $this->roles;

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getDiscordId(): ?string
    {
        return $this->discordId;
    }

    public function setDiscordId(string $discordId): static
    {
        $this->discordId = $discordId;

        return $this;
    }

    public function getAvatarUrl(): ?string
    {
        return $this->avatarUrl;
    }

    public function setAvatarUrl(string $avatarUrl): static
    {
        $this->avatarUrl = $avatarUrl;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return Collection<int, Transaction>
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): static
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions->add($transaction);
            $transaction->setSheriff($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): static
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getSheriff() === $this) {
                $transaction->setSheriff(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, InternalReport>
     */
    public function getInternalReports(): Collection
    {
        return $this->internalReports;
    }

    public function addInternalReport(InternalReport $internalReport): static
    {
        if (!$this->internalReports->contains($internalReport)) {
            $this->internalReports->add($internalReport);
            $internalReport->setSheriff($this);
        }

        return $this;
    }

    public function removeInternalReport(InternalReport $internalReport): static
    {
        if ($this->internalReports->removeElement($internalReport)) {
            // set the owning side to null (unless already changed)
            if ($internalReport->getSheriff() === $this) {
                $internalReport->setSheriff(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Presence>
     */
    public function getPresences(): Collection
    {
        return $this->presences;
    }

    public function addPresence(Presence $presence): static
    {
        if (!$this->presences->contains($presence)) {
            $this->presences->add($presence);
            $presence->setSheriff($this);
        }

        return $this;
    }

    public function removePresence(Presence $presence): static
    {
        if ($this->presences->removeElement($presence)) {
            // set the owning side to null (unless already changed)
            if ($presence->getSheriff() === $this) {
                $presence->setSheriff(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Folder>
     */
    public function getFolders(): Collection
    {
        return $this->folders;
    }

    public function addFolder(Folder $folder): static
    {
        if (!$this->folders->contains($folder)) {
            $this->folders->add($folder);
            $folder->setSheriffInCharge($this);
        }

        return $this;
    }

    public function removeFolder(Folder $folder): static
    {
        if ($this->folders->removeElement($folder)) {
            // set the owning side to null (unless already changed)
            if ($folder->getSheriffInCharge() === $this) {
                $folder->setSheriffInCharge(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, FormationCheck>
     */
    public function getFormationChecks(): Collection
    {
        return $this->formationChecks;
    }

    public function addFormationCheck(FormationCheck $formationCheck): static
    {
        if (!$this->formationChecks->contains($formationCheck)) {
            $this->formationChecks->add($formationCheck);
            $formationCheck->setSheriff($this);
        }

        return $this;
    }

    public function removeFormationCheck(FormationCheck $formationCheck): static
    {
        if ($this->formationChecks->removeElement($formationCheck)) {
            // set the owning side to null (unless already changed)
            if ($formationCheck->getSheriff() === $this) {
                $formationCheck->setSheriff(null);
            }
        }

        return $this;
    }
}
