<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Invoice;

use App\Domain\Invoice\PaymentMethod;
use App\Domain\Invoice\PaymentResult;
use App\Domain\ValueObject\Money;

final class MockPaymentMethod implements PaymentMethod
{
    private bool $shouldSucceed;
    private ?Money $returnAmount;
    private ?string $errorMessage;

    public function __construct(
        bool $shouldSucceed = true,
        ?Money $returnAmount = null,
        ?string $errorMessage = null
    ) {
        $this->shouldSucceed = $shouldSucceed;
        $this->returnAmount = $returnAmount;
        $this->errorMessage = $errorMessage;
    }

    public function processPayment(Money $amount): PaymentResult
    {
        if (!$this->shouldSucceed) {
            return PaymentResult::failed($amount, $this->errorMessage ?? 'Payment failed');
        }

        $actualAmount = $this->returnAmount ?? $amount;
        return PaymentResult::successful($actualAmount);
    }

    public function getName(): string
    {
        return 'Mock Payment Method';
    }
}