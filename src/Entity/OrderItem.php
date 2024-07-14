<?php

namespace App\Entity;

use App\Repository\OrderItemRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Order line item - keeps track of out of stock status (out_of_stock)
 * and restock date (restock_on)
 */
#[ORM\Entity(repositoryClass: OrderItemRepository::class)]
class OrderItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'items')]
    private ?Order $order_root = null;

    #[ORM\Column(length: 255)]
    private ?string $product = null;

    #[ORM\Column]
    private ?bool $out_of_stock = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $restock_on = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderRoot(): ?Order
    {
        return $this->order_root;
    }

    public function setOrderRoot(?Order $order_root): static
    {
        $this->order_root = $order_root;

        return $this;
    }

    public function getProduct(): ?string
    {
        return $this->product;
    }

    public function setProduct(string $product): static
    {
        $this->product = $product;

        return $this;
    }

    public function isOutOfStock(): ?bool
    {
        return $this->out_of_stock;
    }

    public function setOutOfStock(bool $out_of_stock): static
    {
        $this->out_of_stock = $out_of_stock;

        return $this;
    }

    public function getRestockOn(): ?\DateTimeInterface
    {
        return $this->restock_on;
    }

    public function setRestockOn(\DateTimeInterface $restock_on): static
    {
        $this->restock_on = $restock_on;

        return $this;
    }
}
