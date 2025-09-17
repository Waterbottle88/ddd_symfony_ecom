<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\ValueObject;

use App\Domain\Exception\InvalidArgumentException;
use App\Domain\ValueObject\ProductName;
use PHPUnit\Framework\TestCase;

final class ProductNameTest extends TestCase
{
    public function testCanCreateProductName(): void
    {
        $name = ProductName::fromString('Apple iPhone 15');

        $this->assertEquals('Apple iPhone 15', $name->toString());
        $this->assertEquals('Apple iPhone 15', (string) $name);
    }

    public function testThrowsExceptionForEmptyName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Product name cannot be empty');

        ProductName::fromString('');
    }

    public function testThrowsExceptionForWhitespaceOnlyName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Product name cannot be empty');

        ProductName::fromString('   ');
    }

    public function testThrowsExceptionForTooLongName(): void
    {
        $longName = str_repeat('a', 256);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Product name cannot exceed 255 characters');

        ProductName::fromString($longName);
    }

    public function testEquality(): void
    {
        $name1 = ProductName::fromString('Apple iPhone 15');
        $name2 = ProductName::fromString('Apple iPhone 15');
        $name3 = ProductName::fromString('Samsung Galaxy S24');

        $this->assertTrue($name1->equals($name2));
        $this->assertFalse($name1->equals($name3));
    }
}