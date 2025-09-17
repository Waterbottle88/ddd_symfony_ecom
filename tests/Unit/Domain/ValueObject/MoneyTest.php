<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\ValueObject;

use App\Domain\Exception\InvalidArgumentException;
use App\Domain\ValueObject\Money;
use PHPUnit\Framework\TestCase;

final class MoneyTest extends TestCase
{
    public function testCanCreateMoneyFromAmount(): void
    {
        $money = Money::fromAmount(1000);

        $this->assertEquals(1000, $money->getAmount());
        $this->assertEquals(10.00, $money->getAmountFloat());
        $this->assertEquals('UAH', $money->getCurrency());
    }

    public function testCanCreateMoneyFromFloat(): void
    {
        $money = Money::fromFloat(19.99);

        $this->assertEquals(1999, $money->getAmount());
        $this->assertEquals(19.99, $money->getAmountFloat());
    }

    public function testCanAddMoney(): void
    {
        $money1 = Money::fromFloat(10.50);
        $money2 = Money::fromFloat(5.25);

        $result = $money1->add($money2);

        $this->assertEquals(15.75, $result->getAmountFloat());
    }

    public function testCanMultiplyByInteger(): void
    {
        $money = Money::fromFloat(10.00);

        $result = $money->multiply(3);

        $this->assertEquals(30.00, $result->getAmountFloat());
    }

    public function testCanMultiplyByFloat(): void
    {
        $money = Money::fromFloat(10.00);

        $result = $money->multiply(2.5);

        $this->assertEquals(25.00, $result->getAmountFloat());
    }

    public function testThrowsExceptionForNegativeAmount(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Money amount cannot be negative');

        Money::fromAmount(-100);
    }

    public function testThrowsExceptionForNegativeFloat(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Money amount cannot be negative');

        Money::fromFloat(-5.50);
    }

    public function testThrowsExceptionForNegativeMultiplier(): void
    {
        $money = Money::fromFloat(10.00);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Multiplier cannot be negative');

        $money->multiply(-2);
    }

    public function testEquality(): void
    {
        $money1 = Money::fromFloat(10.50);
        $money2 = Money::fromFloat(10.50);
        $money3 = Money::fromFloat(10.51);

        $this->assertTrue($money1->equals($money2));
        $this->assertFalse($money1->equals($money3));
    }

    public function testToString(): void
    {
        $money = Money::fromFloat(19.99);

        $this->assertEquals('19.99 UAH', (string) $money);
    }
}