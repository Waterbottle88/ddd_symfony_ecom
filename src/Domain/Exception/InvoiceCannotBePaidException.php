<?php

declare(strict_types=1);

namespace App\Domain\Exception;

use App\Domain\Invoice\InvoiceStatus;

final class InvoiceCannotBePaidException extends DomainException
{
    public function __construct(InvoiceStatus $status)
    {
        parent::__construct(sprintf(
            'Invoice with status "%s" cannot be paid. Only invoices with status "new" can be paid.',
            $status->value
        ));
    }
}