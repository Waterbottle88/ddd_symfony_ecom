<?php

declare(strict_types=1);

namespace App\Domain\Event;

use App\Domain\ValueObject\ProductId;
use App\Domain\ValueObject\ProductName;

final readonly class ProductCreated extends DomainEvent
{
    public function __construct(
        public ProductId $productId,
        public ProductName $productName,
        \DateTimeImmutable $occurredOn = new \DateTimeImmutable()
    ) {
        parent::__construct($occurredOn);
    }

    public function eventName(): string
    {
        return 'product.created';
    }
}