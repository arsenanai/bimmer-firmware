<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\SoftwareVersionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SoftwareVersionRepository::class)]
#[ORM\Table(name: 'software_version')]
class SoftwareVersion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $systemVersion = null;

    #[ORM\Column(length: 255)]
    private ?string $systemVersionAlt = null;

    #[ORM\Column(length: 1024, nullable: true)]
    private ?string $link = null;

    #[ORM\Column(length: 1024, nullable: true)]
    private ?string $st = null;

    #[ORM\Column(length: 1024, nullable: true)]
    private ?string $gd = null;

    #[ORM\Column]
    private bool $isLatest = false;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $latestDisplayVersion = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSystemVersion(): ?string
    {
        return $this->systemVersion;
    }

    public function setSystemVersion(string $systemVersion): static
    {
        $this->systemVersion = $systemVersion;

        return $this;
    }

    public function getSystemVersionAlt(): ?string
    {
        return $this->systemVersionAlt;
    }

    public function setSystemVersionAlt(string $systemVersionAlt): static
    {
        $this->systemVersionAlt = $systemVersionAlt;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): static
    {
        $this->link = $link ?? '';

        return $this;
    }

    public function getSt(): ?string
    {
        return $this->st;
    }

    public function setSt(?string $st): static
    {
        $this->st = $st ?? '';

        return $this;
    }

    public function getGd(): ?string
    {
        return $this->gd;
    }

    public function setGd(?string $gd): static
    {
        $this->gd = $gd ?? '';

        return $this;
    }

    public function isLatest(): bool
    {
        return $this->isLatest;
    }

    public function setIsLatest(bool $isLatest): static
    {
        $this->isLatest = $isLatest;

        return $this;
    }

    public function getLatestDisplayVersion(): ?string
    {
        return $this->latestDisplayVersion;
    }

    public function setLatestDisplayVersion(?string $latestDisplayVersion): static
    {
        $this->latestDisplayVersion = $latestDisplayVersion;

        return $this;
    }
}
