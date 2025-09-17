<?php

declare(strict_types=1);

namespace App\Domain\Exception;

use App\Domain\Order\OrderStatus;

final class OrderNotEditableException extends DomainException
{
    public function __construct(OrderStatus $currentStatus)
    {
        parent::__construct(sprintf(
            'Order cannot be edited when status is "%s". Only orders with status "new" can be edited.',
            $currentStatus->value
        ));
    }
}