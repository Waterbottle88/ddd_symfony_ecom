<?php

declare(strict_types=1);

namespace App\Domain\Product;

enum ProductType: string
{
    case PIECE = 'piece';
    case WEIGHT = 'weight';

    public function allowsDecimalQuantity(): bool
    {
        return $this === self::WEIGHT;
    }

    public function requiresIntegerQuantity(): bool
    {
        return $this === self::PIECE;
    }
}