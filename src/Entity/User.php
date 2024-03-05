<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email', 'alias'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["users"])]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\Email(
        message: "L'e-mail {{ value }} n'est pas un e-mail valide.",
    )]
    #[Assert\NotBlank]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 25)]
    #[Assert\NotBlank]
    private ?string $firstname = null;

    #[ORM\Column(length: 25)]
    #[Assert\NotBlank]
    private ?string $lastname = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    private ?\DateTimeImmutable $birthAt = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    private ?string $address = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank]
    private ?string $city = null;

    #[ORM\Column(nullable: true)]
    #[Groups(["users"])]
    private ?float $longitude = null;

    #[ORM\Column(nullable: true)]
    #[Groups(["users"])]
    private ?float $latitude = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $avatar = null;

    #[ORM\Column(length: 6)]
    #[Assert\NotBlank]
    private ?string $postalCode = null;

    #[ORM\OneToMany(mappedBy: 'member', targetEntity: Bibliogame::class, orphanRemoval: true)]
    private Collection $bibliogames;

    #[ORM\Column(length: 50, unique: true)]
    #[Assert\NotBlank]
    #[Groups(["users", "events"])]
    private ?string $alias = null;

    #[ORM\OneToMany(mappedBy: 'borrowedBy', targetEntity: Bibliogame::class, orphanRemoval: true)]
    private Collection $myBorrowedGames;

    #[ORM\OneToMany(mappedBy: 'userFrom', targetEntity: Chatting::class, orphanRemoval: true)]
    private Collection $chattingsFrom;

    #[ORM\OneToMany(mappedBy: 'userTo', targetEntity: Chatting::class, orphanRemoval: true)]
    private Collection $chattingsTo;

    #[ORM\OneToMany(mappedBy: 'host', targetEntity: Event::class, orphanRemoval: true)]
    private Collection $events;

    #[ORM\OneToMany(mappedBy: 'requestBy', targetEntity: Bibliogame::class, orphanRemoval: true)]
    private Collection $myBibliogameRequest;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $resetToken = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: EventRequests::class, orphanRemoval: true)]
    private Collection $eventRequests;

    #[ORM\OneToMany(mappedBy: 'member', targetEntity: Review::class, orphanRemoval: true)]
    private Collection $reviews;

    public function __construct()
    {
        $this->bibliogames = new ArrayCollection();
        $this->myBorrowedGames = new ArrayCollection();
        $this->chattings = new ArrayCollection();
        $this->events = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->eventRequests = new ArrayCollection();
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
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

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

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getBirthAt(): ?\DateTimeImmutable
    {
        return $this->birthAt;
    }

    public function setBirthAt(\DateTimeImmutable $birthAt): static
    {
        $this->birthAt = $birthAt;

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

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): static
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): static
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function __toString()
    {
        //return ucwords($this->firstname.' '.$this->lastname);
        return ucwords($this->alias);
    }

    /**
     * @return Collection<int, Bibliogame>
     */
    public function getBibliogames(): Collection
    {
        return $this->bibliogames;
    }

    public function addBibliogame(Bibliogame $bibliogame): static
    {
        if (!$this->bibliogames->contains($bibliogame)) {
            $this->bibliogames->add($bibliogame);
            $bibliogame->setMember($this);
        }

        return $this;
    }

    public function removeBibliogame(Bibliogame $bibliogame): static
    {
        if ($this->bibliogames->removeElement($bibliogame)) {
            // set the owning side to null (unless already changed)
            if ($bibliogame->getMember() === $this) {
                $bibliogame->setMember(null);
            }
        }

        return $this;
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function setAlias(string $alias): static
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * @return Collection<int, Bibliogame>
     */
    public function getMyBorrowedGames(): Collection
    {
        return $this->myBorrowedGames;
    }

    public function addMyBorrowedGame(Bibliogame $myBorrowedGame): static
    {
        if (!$this->myBorrowedGames->contains($myBorrowedGame)) {
            $this->myBorrowedGames->add($myBorrowedGame);
            $myBorrowedGame->setBorrowedBy($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Chatting>
     */
    public function getChattingsFrom(): Collection
    {
        return $this->chattingsFrom;
    }

    public function addChattingFrom(Chatting $chatting): static
    {
        if (!$this->chattingsFrom->contains($chatting)) {
            $this->chattingsFrom->add($chatting);
            $chatting->setUseTo($this);
        }

        return $this;
    }
 /**
     * @return Collection<int, Chatting>
     */

     public function getChattingsTo()
     {
        return $this->chattingsTo;
     }
    public function removeMyBorrowedGame(Bibliogame $myBorrowedGame): static
    {
        if ($this->myBorrowedGames->removeElement($myBorrowedGame)) {
            // set the owning side to null (unless already changed)
            if ($myBorrowedGame->getBorrowedBy() === $this) {
                $myBorrowedGame->setBorrowedBy(null);
            }
        }

        return $this;
    }

    public function removeChatting(Chatting $chatting): static
    {
        if ($this->chattings->removeElement($chatting)) {
            // set the owning side to null (unless already changed)
            if ($chatting->getUserFrom() === $this) {
                $chatting->setUserFrom(null);
            }
        }

        return $this;
    }

    /**
     * Return array of Game
     *
     * @return Game[]
     */
    public function getGameCollection()
    {
        $games = [];
        $bibliogames = $this->getBibliogames();

        foreach ($bibliogames as $b) {
            $games[] = $b->getGame();
        }
        return $games;
    }
    /**
     * 
     * Return array of game
     * @return Game[]
     */

     public function getGamesReviewed()
     {
        $games=[];

        foreach($this->reviews as $review)
        {
            $games[]=$review->getGame();
        }
        return $games;
     }

    /**
     * @return Collection<int, Event>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): static
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
            $event->setHost($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): static
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getHost() === $this) {
                $event->setHost(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Bibliogame>
     */
    public function getMyBibliogameRequest(): Collection
    {
        return $this->myBibliogameRequest;
    }

    public function addMyBibliogameRequest(Bibliogame $myBibliogameRequest): static
    {
        if (!$this->myBibliogameRequest->contains($myBibliogameRequest)) {
            $this->myBibliogameRequest->add($myBibliogameRequest);
            $myBibliogameRequest->setRequestBy($this);
        }

        return $this;
    }

    public function removeMyBibliogameRequest(Bibliogame $myBibliogameRequest): static
    {
        if ($this->myBibliogameRequest->removeElement($myBibliogameRequest)) {
            // set the owning side to null (unless already changed)
            if ($myBibliogameRequest->getRequestBy() === $this) {
                $myBibliogameRequest->setRequestBy(null);
            }
        }

        return $this;
    }

    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetToken(?string $resetToken): static
    {
        $this->resetToken = $resetToken;

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
            $eventRequest->setUser($this);
        }

        return $this;
    }

    public function removeEventRequest(EventRequests $eventRequest): static
    {
        if ($this->eventRequests->removeElement($eventRequest)) {
            // set the owning side to null (unless already changed)
            if ($eventRequest->getUser() === $this) {
                $eventRequest->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Review>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): static
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setMember($this);
        }

        return $this;
    }

    public function removeReview(Review $review): static
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getMember() === $this) {
                $review->setMember(null);
            }
        }

        return $this;
    }
}
