<?php

declare(strict_types=1);

namespace App\Domain\Invoice;

enum InvoiceStatus: string
{
    case NEW = 'new';
    case CANCELLED = 'cancelled';
    case PAID = 'paid';

    public function canBePaid(): bool
    {
        return $this === self::NEW;
    }

    public function canBeCancelled(): bool
    {
        return $this === self::NEW;
    }

    public function isPaid(): bool
    {
        return $this === self::PAID;
    }
}