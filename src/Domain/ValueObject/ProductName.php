<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use App\Domain\Exception\InvalidArgumentException;

final readonly class ProductName
{
    private function __construct(
        private string $value
    ) {
        if (empty(trim($value))) {
            throw new InvalidArgumentException('Product name cannot be empty');
        }

        if (strlen($value) > 255) {
            throw new InvalidArgumentException('Product name cannot exceed 255 characters');
        }
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function equals(ProductName $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}