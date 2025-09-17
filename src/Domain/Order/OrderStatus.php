<?php

declare(strict_types=1);

namespace App\Domain\Order;

enum OrderStatus: string
{
    case NEW = 'new';
    case INVOICED = 'invoiced';
    case PAID = 'paid';

    public function allowsEditing(): bool
    {
        return $this === self::NEW;
    }

    public function canCreateInvoice(): bool
    {
        return $this !== self::PAID;
    }
}