<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\MappedSuperclass]
abstract class Item
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\Column]
    #[Assert\Positive]
    private ?int $weight = null;

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

    public function getWeight(?string $unit = 'g'): float
    {
        return ($unit === 'kg') ? $this->weight / 1000 : $this->weight;
    }

    public function setWeight(int $weight, ?string $unit = 'g'): static
    {
        $this->weight = ($unit === 'kg') ? $weight * 1000 : $weight;

        return $this;
    }
}
