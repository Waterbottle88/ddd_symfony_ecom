<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\Invoice\Invoice;
use App\Domain\Order\Order;
use App\Infrastructure\Payment\MockCreditCardPayment;

final class InvoiceService
{
    public function createInvoiceForOrder(Order $order): Invoice
    {
        return $order->issueInvoice();
    }

    public function payInvoice(Invoice $invoice): void
    {
        $paymentMethod = new MockCreditCardPayment();
        $invoice->pay($paymentMethod);
    }
}