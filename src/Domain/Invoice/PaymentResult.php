<?php

declare(strict_types=1);

namespace App\Domain\Invoice;

use App\Domain\ValueObject\Money;

final readonly class PaymentResult
{
    private function __construct(
        private bool $successful,
        private Money $amount,
        private ?string $errorMessage = null
    ) {
    }

    public static function successful(Money $amount): self
    {
        return new self(true, $amount);
    }

    public static function failed(Money $amount, string $errorMessage): self
    {
        return new self(false, $amount, $errorMessage);
    }

    public function isSuccessful(): bool
    {
        return $this->successful;
    }

    public function getAmount(): Money
    {
        return $this->amount;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }
}