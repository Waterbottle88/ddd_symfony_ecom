<?php

declare(strict_types=1);

namespace App\Domain\Event;

use App\Domain\ValueObject\OrderId;

final readonly class OrderCreated extends DomainEvent
{
    public function __construct(
        public OrderId $orderId,
        \DateTimeImmutable $occurredOn = new \DateTimeImmutable()
    ) {
        parent::__construct($occurredOn);
    }

    public function eventName(): string
    {
        return 'order.created';
    }
}