<?php

declare(strict_types=1);

namespace App\Application\DTO;

use App\Domain\Exception\InvalidArgumentException;

final readonly class CreateProductRequest
{
    public function __construct(
        public string $name,
        public float $price,
        public string $type
    ) {
        $this->validate();
    }

    private function validate(): void
    {
        if (empty(trim($this->name))) {
            throw new InvalidArgumentException('Product name cannot be empty');
        }

        if ($this->price < 0) {
            throw new InvalidArgumentException('Product price cannot be negative');
        }

        if (empty(trim($this->type))) {
            throw new InvalidArgumentException('Product type cannot be empty');
        }
    }
}