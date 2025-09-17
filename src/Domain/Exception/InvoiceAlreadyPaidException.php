<?php

declare(strict_types=1);

namespace App\Domain\Exception;

final class InvoiceAlreadyPaidException extends DomainException
{
    public function __construct()
    {
        parent::__construct('Invoice has already been paid and cannot be paid again');
    }
}