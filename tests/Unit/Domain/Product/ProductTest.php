<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Product;

use App\Domain\Exception\InvalidQuantityForProductTypeException;
use App\Domain\Product\Product;
use App\Domain\Product\ProductType;
use App\Domain\ValueObject\Money;
use App\Domain\ValueObject\ProductName;
use App\Domain\ValueObject\Quantity;
use PHPUnit\Framework\TestCase;

final class ProductTest extends TestCase
{
    public function testCanCreatePieceProduct(): void
    {
        $name = ProductName::fromString('Apple iPhone 15');
        $price = Money::fromFloat(999.99);
        $type = ProductType::PIECE;

        $product = Product::create($name, $price, $type);

        $this->assertEquals($name, $product->getName());
        $this->assertEquals($price, $product->getPrice());
        $this->assertEquals($type, $product->getType());
    }

    public function testCanCreateWeightProduct(): void
    {
        $name = ProductName::fromString('Organic Rice');
        $price = Money::fromFloat(2.50);
        $type = ProductType::WEIGHT;

        $product = Product::create($name, $price, $type);

        $this->assertEquals($type, $product->getType());
    }

    public function testPieceProductAcceptsIntegerQuantity(): void
    {
        $product = Product::create(
            ProductName::fromString('iPhone'),
            Money::fromFloat(999.99),
            ProductType::PIECE
        );

        $quantity = Quantity::fromInt(2);

        $this->expectNotToPerformAssertions();
        $product->validateQuantity($quantity);
    }

    public function testPieceProductRejectsDecimalQuantity(): void
    {
        $product = Product::create(
            ProductName::fromString('iPhone'),
            Money::fromFloat(999.99),
            ProductType::PIECE
        );

        $quantity = Quantity::fromFloat(2.5);

        $this->expectException(InvalidQuantityForProductTypeException::class);
        $this->expectExceptionMessage('Piece products require integer quantities, got 2.50');

        $product->validateQuantity($quantity);
    }

    public function testWeightProductAcceptsDecimalQuantity(): void
    {
        $product = Product::create(
            ProductName::fromString('Rice'),
            Money::fromFloat(2.50),
            ProductType::WEIGHT
        );

        $quantity = Quantity::fromFloat(1.5);

        $this->expectNotToPerformAssertions();
        $product->validateQuantity($quantity);
    }

    public function testWeightProductAcceptsIntegerQuantity(): void
    {
        $product = Product::create(
            ProductName::fromString('Rice'),
            Money::fromFloat(2.50),
            ProductType::WEIGHT
        );

        $quantity = Quantity::fromInt(2);

        $this->expectNotToPerformAssertions();
        $product->validateQuantity($quantity);
    }

    public function testCalculatesTotalPriceCorrectly(): void
    {
        $product = Product::create(
            ProductName::fromString('Rice'),
            Money::fromFloat(2.50),
            ProductType::WEIGHT
        );

        $quantity = Quantity::fromFloat(1.5);
        $totalPrice = $product->calculateTotalPrice($quantity);

        $this->assertEquals(3.75, $totalPrice->getAmountFloat());
    }

    public function testCanChangePrice(): void
    {
        $product = Product::create(
            ProductName::fromString('iPhone'),
            Money::fromFloat(999.99),
            ProductType::PIECE
        );

        $newPrice = Money::fromFloat(899.99);
        $product->changePrice($newPrice);

        $this->assertEquals($newPrice, $product->getPrice());
    }
}