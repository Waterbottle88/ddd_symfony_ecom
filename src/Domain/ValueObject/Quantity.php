<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use App\Domain\Exception\InvalidArgumentException;

final readonly class Quantity
{
    private function __construct(
        private float $value
    ) {
        if ($value <= 0) {
            throw new InvalidArgumentException('Quantity must be positive');
        }
    }

    public static function fromFloat(float $value): self
    {
        return new self($value);
    }

    public static function fromInt(int $value): self
    {
        return new self((float) $value);
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function isInteger(): bool
    {
        return floor($this->value) === $this->value;
    }

    public function equals(Quantity $other): bool
    {
        return abs($this->value - $other->value) < 0.0001;
    }

    public function __toString(): string
    {
        return $this->isInteger() ? (string) (int) $this->value : (string) $this->value;
    }
}