<?php

declare(strict_types=1);

namespace App\Domain\Order;

use App\Domain\Product\Product;
use App\Domain\ValueObject\Money;
use App\Domain\ValueObject\Quantity;

final class OrderItem
{
    private Money $totalPrice;

    private function __construct(
        private Product $product,
        private Quantity $quantity
    ) {
        $this->product->validateQuantity($this->quantity);
        $this->totalPrice = $this->product->calculateTotalPrice($this->quantity);
    }

    public static function create(Product $product, Quantity $quantity): self
    {
        return new self($product, $quantity);
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getQuantity(): Quantity
    {
        return $this->quantity;
    }

    public function getTotalPrice(): Money
    {
        return $this->totalPrice;
    }

    public function recalculatePrice(): void
    {
        $this->totalPrice = $this->product->calculateTotalPrice($this->quantity);
    }
}