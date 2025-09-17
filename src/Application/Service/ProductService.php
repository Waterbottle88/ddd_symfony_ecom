<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Application\DTO\CreateProductRequest;
use App\Domain\Product\Product;
use App\Domain\Product\ProductRepositoryInterface;
use App\Domain\Product\ProductType;
use App\Domain\ValueObject\Money;
use App\Domain\ValueObject\ProductName;

final class ProductService
{
    public function __construct(
        private ProductRepositoryInterface $productRepository
    ) {
    }

    public function createProduct(string $name, float $price, string $type): Product
    {
        $productName = ProductName::fromString($name);
        $productPrice = Money::fromFloat($price);
        $productType = ProductType::from($type);

        $product = Product::create($productName, $productPrice, $productType);
        $this->productRepository->save($product);

        return $product;
    }

    public function createProductFromRequest(CreateProductRequest $request): Product
    {
        return $this->createProduct($request->name, $request->price, $request->type);
    }

    /** @return Product[] */
    public function getAllProducts(): array
    {
        return $this->productRepository->findAll();
    }

    public function findProductByName(string $name): ?Product
    {
        return $this->productRepository->findByName(ProductName::fromString($name));
    }
}