<?php

declare(strict_types=1);

namespace App\Domain\Invoice;

use App\Domain\ValueObject\Money;

interface PaymentMethod
{
    public function processPayment(Money $amount): PaymentResult;

    public function getName(): string;
}