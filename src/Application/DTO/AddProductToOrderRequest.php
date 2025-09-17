<?php

declare(strict_types=1);

namespace App\Application\DTO;

use App\Domain\Exception\InvalidArgumentException;

final readonly class AddProductToOrderRequest
{
    public function __construct(
        public int $orderId,
        public string $productName,
        public float $quantity
    ) {
        $this->validate();
    }

    private function validate(): void
    {
        if ($this->orderId <= 0) {
            throw new InvalidArgumentException('Order ID must be positive');
        }

        if (empty(trim($this->productName))) {
            throw new InvalidArgumentException('Product name cannot be empty');
        }

        if ($this->quantity <= 0) {
            throw new InvalidArgumentException('Quantity must be positive');
        }
    }
}