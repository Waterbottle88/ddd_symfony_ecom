<?php

declare(strict_types=1);

namespace App\Domain\Product;

use App\Domain\Exception\InvalidQuantityForProductTypeException;
use App\Domain\ValueObject\Money;
use App\Domain\ValueObject\ProductName;
use App\Domain\ValueObject\Quantity;

final class Product
{
    private function __construct(
        private ProductName $name,
        private Money $price,
        private ProductType $type
    ) {
    }

    public static function create(
        ProductName $name,
        Money $price,
        ProductType $type
    ): self {
        return new self($name, $price, $type);
    }

    public function validateQuantity(Quantity $quantity): void
    {
        if ($this->type->requiresIntegerQuantity() && !$quantity->isInteger()) {
            throw new InvalidQuantityForProductTypeException($this->type, $quantity->getValue());
        }
    }

    public function calculateTotalPrice(Quantity $quantity): Money
    {
        $this->validateQuantity($quantity);
        return $this->price->multiply($quantity->getValue());
    }

    public function getName(): ProductName
    {
        return $this->name;
    }

    public function getPrice(): Money
    {
        return $this->price;
    }

    public function getType(): ProductType
    {
        return $this->type;
    }

    public function changePrice(Money $newPrice): void
    {
        $this->price = $newPrice;
    }
}