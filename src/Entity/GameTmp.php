<?php

namespace App\Entity;

use App\Repository\GameTmpRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameTmpRepository::class)]
class GameTmp
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 500)]
    private ?string $name = null;

    #[ORM\Column(length: 1000)]
    private ?string $href = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id){

        $this->id=$id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getHref(): ?string
    {
        return $this->href;
    }

    public function setHref(string $href): static
    {
        $this->href = $href;

        return $this;
    }
}
