<?php

declare(strict_types=1);

namespace App\Domain\Exception;

use App\Domain\ValueObject\Money;

final class PaymentAmountMismatchException extends DomainException
{
    public function __construct(Money $expectedAmount, Money $actualAmount)
    {
        parent::__construct(sprintf(
            'Payment amount mismatch. Expected: %s, but received: %s',
            $expectedAmount,
            $actualAmount
        ));
    }
}