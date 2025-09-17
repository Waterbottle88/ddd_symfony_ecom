<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use App\Domain\Exception\InvalidArgumentException;
use Money\Currency;
use Money\Money as MoneyPHP;

final readonly class Money
{
    private MoneyPHP $amount;

    private function __construct(MoneyPHP $amount)
    {
        $this->amount = $amount;
    }

    public static function fromAmount(int $amount, string $currency = 'UAH'): self
    {
        if ($amount < 0) {
            throw new InvalidArgumentException('Money amount cannot be negative');
        }

        return new self(new MoneyPHP($amount, new Currency($currency)));
    }

    public static function fromFloat(float $amount, string $currency = 'UAH'): self
    {
        if ($amount < 0) {
            throw new InvalidArgumentException('Money amount cannot be negative');
        }

        $cents = (int) round($amount * 100);
        return new self(new MoneyPHP($cents, new Currency($currency)));
    }

    public function add(Money $other): self
    {
        return new self($this->amount->add($other->amount));
    }

    public function multiply(int|float $multiplier): self
    {
        if ($multiplier < 0) {
            throw new InvalidArgumentException('Multiplier cannot be negative');
        }

        if (is_float($multiplier)) {
            return new self($this->amount->multiply((string) $multiplier));
        }

        return new self($this->amount->multiply($multiplier));
    }

    public function equals(Money $other): bool
    {
        return $this->amount->equals($other->amount);
    }

    public function getAmount(): int
    {
        return (int) $this->amount->getAmount();
    }

    public function getAmountFloat(): float
    {
        return $this->getAmount() / 100;
    }

    public function getCurrency(): string
    {
        return $this->amount->getCurrency()->getCode();
    }

    public function __toString(): string
    {
        return sprintf('%.2f %s', $this->getAmountFloat(), $this->getCurrency());
    }
}