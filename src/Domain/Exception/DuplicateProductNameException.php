<?php

declare(strict_types=1);

namespace App\Domain\Exception;

final class DuplicateProductNameException extends DomainException
{
    public function __construct(string $productName)
    {
        parent::__construct(sprintf('Product with name "%s" already exists', $productName));
    }
}