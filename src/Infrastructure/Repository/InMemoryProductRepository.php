<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Exception\DuplicateProductNameException;
use App\Domain\Product\Product;
use App\Domain\Product\ProductRepositoryInterface;
use App\Domain\ValueObject\ProductName;
use Symfony\Component\HttpFoundation\RequestStack;

final class InMemoryProductRepository implements ProductRepositoryInterface
{
    private const SESSION_KEY = 'ecommerce_products';

    public function __construct(
        private RequestStack $requestStack
    ) {
    }

    public function save(Product $product): void
    {
        if ($this->existsByName($product->getName())) {
            throw new DuplicateProductNameException($product->getName()->toString());
        }

        $products = $this->getProducts();
        $products[] = $product;
        $this->setProducts($products);
    }

    public function findByName(ProductName $name): ?Product
    {
        foreach ($this->getProducts() as $product) {
            if ($product->getName()->equals($name)) {
                return $product;
            }
        }

        return null;
    }

    public function existsByName(ProductName $name): bool
    {
        return $this->findByName($name) !== null;
    }

    /** @return Product[] */
    public function findAll(): array
    {
        return $this->getProducts();
    }

    public function clear(): void
    {
        $this->setProducts([]);
    }

    /** @return Product[] */
    private function getProducts(): array
    {
        $session = $this->requestStack->getSession();
        return $session->get(self::SESSION_KEY, []);
    }

    /** @param Product[] $products */
    private function setProducts(array $products): void
    {
        $session = $this->requestStack->getSession();
        $session->set(self::SESSION_KEY, $products);
    }
}