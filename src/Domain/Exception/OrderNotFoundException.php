<?php

declare(strict_types=1);

namespace App\Domain\Exception;

final class OrderNotFoundException extends DomainException
{
    public function __construct(int $orderId)
    {
        parent::__construct(sprintf('Order with ID %d not found', $orderId));
    }
}