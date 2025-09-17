<?php

declare(strict_types=1);

namespace App\Domain\Exception;

final class ProductNotFoundException extends DomainException
{
    public function __construct(string $productName)
    {
        parent::__construct(sprintf('Product "%s" not found', $productName));
    }
}