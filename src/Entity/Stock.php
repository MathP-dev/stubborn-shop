<?php

namespace App\Entity;

use App\Repository\StockRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StockRepository::class)]
class Stock
{
    public const SIZES = ['XS', 'S', 'M', 'L', 'XL'];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'stocks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    #[ORM\Column(length: 10)]
    private ? string $size = null;

    #[ORM\Column]
    private int $quantity = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;
        return $this;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(string $size): static
    {
        $this->size = $size;
        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function decreaseQuantity(int $amount = 1): static
    {
        $this->quantity = max(0, $this->quantity - $amount);
        return $this;
    }

    public function increaseQuantity(int $amount = 1): static
    {
        $this->quantity += $amount;
        return $this;
    }

    public function isAvailable(): bool
    {
        return $this->quantity > 0;
    }
}