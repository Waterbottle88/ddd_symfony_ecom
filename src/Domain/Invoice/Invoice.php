<?php

declare(strict_types=1);

namespace App\Domain\Invoice;

use App\Domain\Exception\InvoiceAlreadyPaidException;
use App\Domain\Exception\InvoiceCannotBePaidException;
use App\Domain\Exception\PaymentAmountMismatchException;
use App\Domain\Exception\PaymentFailedException;
use App\Domain\Order\Order;
use App\Domain\ValueObject\Money;

final class Invoice
{
    private InvoiceStatus $status;
    private Money $amount;

    private function __construct(
        private Order $order
    ) {
        $this->status = InvoiceStatus::NEW;
        $this->amount = $this->order->calculateTotalAmount();
    }

    public static function createForOrder(Order $order): self
    {
        return new self($order);
    }

    public function pay(PaymentMethod $paymentMethod): void
    {
        if ($this->status->isPaid()) {
            throw new InvoiceAlreadyPaidException();
        }

        if (!$this->status->canBePaid()) {
            throw new InvoiceCannotBePaidException($this->status);
        }

        $paymentResult = $paymentMethod->processPayment($this->amount);

        if (!$paymentResult->isSuccessful()) {
            throw new PaymentFailedException($paymentResult->getErrorMessage() ?? 'Unknown payment error');
        }

        if (!$this->amount->equals($paymentResult->getAmount())) {
            throw new PaymentAmountMismatchException($this->amount, $paymentResult->getAmount());
        }

        $this->status = InvoiceStatus::PAID;
        $this->order->markAsPaid();
    }

    public function cancel(): void
    {
        if (!$this->status->canBeCancelled()) {
            return;
        }

        $this->status = InvoiceStatus::CANCELLED;
    }

    public function getStatus(): InvoiceStatus
    {
        return $this->status;
    }

    public function getAmount(): Money
    {
        return $this->amount;
    }

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function isNew(): bool
    {
        return $this->status === InvoiceStatus::NEW;
    }

    public function isPaid(): bool
    {
        return $this->status->isPaid();
    }

    public function isCancelled(): bool
    {
        return $this->status === InvoiceStatus::CANCELLED;
    }
}