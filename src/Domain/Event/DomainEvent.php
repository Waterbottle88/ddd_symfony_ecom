<?php

declare(strict_types=1);

namespace App\Domain\Event;

abstract readonly class DomainEvent
{
    public function __construct(
        private \DateTimeImmutable $occurredOn = new \DateTimeImmutable()
    ) {
    }

    public function occurredOn(): \DateTimeImmutable
    {
        return $this->occurredOn;
    }

    abstract public function eventName(): string;
}