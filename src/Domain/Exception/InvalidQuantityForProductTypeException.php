<?php

declare(strict_types=1);

namespace App\Domain\Exception;

use App\Domain\Product\ProductType;

final class InvalidQuantityForProductTypeException extends DomainException
{
    public function __construct(ProductType $productType, float $quantity)
    {
        $message = match ($productType) {
            ProductType::PIECE => sprintf('Piece products require integer quantities, got %.2f', $quantity),
            ProductType::WEIGHT => sprintf('Invalid quantity %.2f for weight product', $quantity),
        };

        parent::__construct($message);
    }
}