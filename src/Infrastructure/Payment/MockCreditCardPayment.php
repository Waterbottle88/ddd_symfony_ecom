<?php

declare(strict_types=1);

namespace App\Infrastructure\Payment;

use App\Domain\Invoice\PaymentMethod;
use App\Domain\Invoice\PaymentResult;
use App\Domain\ValueObject\Money;

final class MockCreditCardPayment implements PaymentMethod
{
    public function processPayment(Money $amount): PaymentResult
    {
        // Simulate payment processing
        // In real implementation, this would integrate with payment gateway
        return PaymentResult::successful($amount);
    }

    public function getName(): string
    {
        return 'Credit Card';
    }
}