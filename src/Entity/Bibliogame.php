<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\BibliogameRepository;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: BibliogameRepository::class)]
#[Groups(["bibliogame"])]
class Bibliogame
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isAvailable = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $borrowedAt = null;

    #[ORM\ManyToOne(inversedBy: 'bibliogames')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Game $game = null;

    #[ORM\ManyToOne(inversedBy: 'bibliogames')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $member = null;

    #[ORM\ManyToOne(inversedBy: 'myBorrowedGames')]
    private ?User $borrowedBy = null;

    #[ORM\ManyToOne(inversedBy: 'myBibliogameRequest')]
    private ?User $requestBy = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isIsAvailable(): ?bool
    {
        return $this->isAvailable;
    }

    public function setIsAvailable(?bool $isAvailable): static
    {
        $this->isAvailable = $isAvailable;

        return $this;
    }

    public function getBorrowedAt(): ?\DateTimeImmutable
    {
        return $this->borrowedAt;
    }

    public function setBorrowedAt(?\DateTimeImmutable $borrowedAt): static
    {
        $this->borrowedAt = $borrowedAt;

        return $this;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): static
    {
        $this->game = $game;

        return $this;
    }

    public function getMember(): ?User
    {
        return $this->member;
    }

    public function setMember(?User $member): static
    {
        $this->member = $member;

        return $this;
    }

    public function __toString()
    {
        return $this->getGame();
    }

    public function getBorrowedBy(): ?User
    {
        return $this->borrowedBy;
    }

    public function setBorrowedBy(?User $borrowedBy): static
    {
        $this->borrowedBy = $borrowedBy;

        return $this;
    }

    public function getRequestBy(): ?User
    {
        return $this->requestBy;
    }

    public function setRequestBy(?User $requestBy): static
    {
        $this->requestBy = $requestBy;

        return $this;
    }
}
