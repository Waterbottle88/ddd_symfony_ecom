<?php

declare(strict_types=1);

namespace App\Domain\Event;

use App\Domain\ValueObject\OrderId;
use App\Domain\ValueObject\Money;

final readonly class InvoicePaid extends DomainEvent
{
    public function __construct(
        public OrderId $orderId,
        public Money $amount,
        \DateTimeImmutable $occurredOn = new \DateTimeImmutable()
    ) {
        parent::__construct($occurredOn);
    }

    public function eventName(): string
    {
        return 'invoice.paid';
    }
}