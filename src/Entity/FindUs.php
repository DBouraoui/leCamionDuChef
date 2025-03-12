<?php

namespace App\Entity;

use App\Repository\FindUsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FindUsRepository::class)]
class FindUs
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $day = null;

    #[ORM\Column(length: 255)]
    private ?string $morningTime = null;

    #[ORM\Column(length: 255)]
    private ?string $morningLocalisation = null;

    #[ORM\Column(length: 255)]
    private ?string $eveningTime = null;

    #[ORM\Column(length: 255)]
    private ?string $eveningLocalisation = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDay(): ?string
    {
        return $this->day;
    }

    public function setDay(string $day): static
    {
        $this->day = $day;

        return $this;
    }

    public function getMorningTime(): ?string
    {
        return $this->morningTime;
    }

    public function setMorningTime(string $morningTime): static
    {
        $this->morningTime = $morningTime;

        return $this;
    }

    public function getMorningLocalisation(): ?string
    {
        return $this->morningLocalisation;
    }

    public function setMorningLocalisation(string $morningLocalisation): static
    {
        $this->morningLocalisation = $morningLocalisation;

        return $this;
    }

    public function getEveningTime(): ?string
    {
        return $this->eveningTime;
    }

    public function setEveningTime(string $eveningTime): static
    {
        $this->eveningTime = $eveningTime;

        return $this;
    }

    public function getEveningLocalisation(): ?string
    {
        return $this->eveningLocalisation;
    }

    public function setEveningLocalisation(string $eveningLocalisation): static
    {
        $this->eveningLocalisation = $eveningLocalisation;

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
}
