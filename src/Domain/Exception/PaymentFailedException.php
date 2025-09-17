<?php

declare(strict_types=1);

namespace App\Domain\Exception;

final class PaymentFailedException extends DomainException
{
    public function __construct(string $reason)
    {
        parent::__construct(sprintf('Payment failed: %s', $reason));
    }
}