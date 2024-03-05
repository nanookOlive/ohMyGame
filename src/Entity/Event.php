<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use App\Repository\EventRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["events"])]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Groups(["events"])]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 3,
        max: 50,
        minMessage: 'Le titre doit au moins faire {{ limit }} caractères de long',
        maxMessage: 'Le titre ne doit pas faire plus de {{ limit }} caractères',
    )]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Le nom de l\'image est trop long',
    )]
    private ?string $picture = null;

    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero]
    private ?int $playersMin = null;

    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero]
    private ?int $playersMax = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Adresse trop longue',
    )]
    private ?string $address = null;

    #[ORM\Column]
    #[Groups(["events"])]
    private ?float $latitude = null;

    #[ORM\Column]
    #[Groups(["events"])]
    private ?float $longitude = null;

    #[ORM\Column]
    private ?bool $isPublic = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    private ?\DateTimeImmutable $startAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $endAt = null;

    #[ORM\ManyToOne(inversedBy: 'events')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["events"])]
    private ?User $host = null;

    #[ORM\ManyToMany(targetEntity: Game::class, inversedBy: 'events')]
    private Collection $games;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: EventRequests::class, orphanRemoval: true)]
    private Collection $eventRequests;

    public function __construct()
    {
        $this->games = new ArrayCollection();
        $this->isPublic = false;
        $this->eventRequests = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): static
    {
        $this->picture = $picture;

        return $this;
    }

    public function getPlayersMin(): ?int
    {
        return $this->playersMin;
    }

    public function setPlayersMin(?int $playersMin): static
    {
        $this->playersMin = $playersMin;

        return $this;
    }

    public function getPlayersMax(): ?int
    {
        return $this->playersMax;
    }

    public function setPlayersMax(?int $playersMax): static
    {
        $this->playersMax = $playersMax;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function isIsPublic(): ?bool
    {
        return $this->isPublic;
    }

    public function setIsPublic(bool $isPublic): static
    {
        $this->isPublic = $isPublic;

        return $this;
    }

    public function getStartAt(): ?\DateTimeImmutable
    {
        return $this->startAt;
    }

    public function setStartAt(\DateTimeImmutable $startAt): static
    {
        $this->startAt = $startAt;

        return $this;
    }

    public function getEndAt(): ?\DateTimeImmutable
    {
        return $this->endAt;
    }

    public function setEndAt(?\DateTimeImmutable $endAt): static
    {
        $this->endAt = $endAt;

        return $this;
    }

    public function getHost(): ?User
    {
        return $this->host;
    }

    public function setHost(?User $host): static
    {
        $this->host = $host;

        return $this;
    }

    /**
     * @return Collection<int, Game>
     */
    public function getGames(): Collection
    {
        return $this->games;
    }

    public function addGame(Game $game): static
    {
        if (!$this->games->contains($game)) {
            $this->games->add($game);
        }

        return $this;
    }

    public function removeGame(Game $game): static
    {
        $this->games->removeElement($game);

        return $this;
    }

    /**
     * @return Collection<int, EventRequests>
     */
    public function getEventRequests(): Collection
    {
        return $this->eventRequests;
    }

    public function addEventRequest(EventRequests $eventRequest): static
    {
        if (!$this->eventRequests->contains($eventRequest)) {
            $this->eventRequests->add($eventRequest);
            $eventRequest->setEvent($this);
        }

        return $this;
    }

    public function removeEventRequest(EventRequests $eventRequest): static
    {
        if ($this->eventRequests->removeElement($eventRequest)) {
            // set the owning side to null (unless already changed)
            if ($eventRequest->getEvent() === $this) {
                $eventRequest->setEvent(null);
            }
        }

        return $this;
    }

    /**
     * Return array of User with status ACCEPTED
     *
     * @return User[]
     */
    public function getAcceptedUsers()
    {
        $users = [];
        $eventRequest = $this->getEventRequests();

        foreach ($eventRequest as $e) {
            if ($e->getStatus() === EventRequests::ACCEPTED) {
                $users[] = $e->getUser();
            }
        }
        return $users;
    }
}
