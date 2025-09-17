<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\ValueObject;

use App\Domain\Exception\InvalidArgumentException;
use App\Domain\ValueObject\Quantity;
use PHPUnit\Framework\TestCase;

final class QuantityTest extends TestCase
{
    public function testCanCreateQuantityFromFloat(): void
    {
        $quantity = Quantity::fromFloat(2.5);

        $this->assertEquals(2.5, $quantity->getValue());
        $this->assertFalse($quantity->isInteger());
        $this->assertEquals('2.5', (string) $quantity);
    }

    public function testCanCreateQuantityFromInt(): void
    {
        $quantity = Quantity::fromInt(3);

        $this->assertEquals(3.0, $quantity->getValue());
        $this->assertTrue($quantity->isInteger());
        $this->assertEquals('3', (string) $quantity);
    }

    public function testThrowsExceptionForZeroQuantity(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Quantity must be positive');

        Quantity::fromFloat(0.0);
    }

    public function testThrowsExceptionForNegativeQuantity(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Quantity must be positive');

        Quantity::fromFloat(-1.5);
    }

    public function testEquality(): void
    {
        $quantity1 = Quantity::fromFloat(2.5);
        $quantity2 = Quantity::fromFloat(2.5);
        $quantity3 = Quantity::fromFloat(2.6);

        $this->assertTrue($quantity1->equals($quantity2));
        $this->assertFalse($quantity1->equals($quantity3));
    }

    public function testEqualityWithSmallDifference(): void
    {
        $quantity1 = Quantity::fromFloat(2.5);
        $quantity2 = Quantity::fromFloat(2.501);

        $this->assertFalse($quantity1->equals($quantity2));
    }
}