<?php

declare(strict_types=1);

namespace App\Domain\Product;

use App\Domain\ValueObject\ProductName;

interface ProductRepositoryInterface
{
    public function save(Product $product): void;

    public function findByName(ProductName $name): ?Product;

    public function existsByName(ProductName $name): bool;

    /** @return Product[] */
    public function findAll(): array;

    public function clear(): void;
}