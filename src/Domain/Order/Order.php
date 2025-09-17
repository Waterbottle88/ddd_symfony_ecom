<?php

declare(strict_types=1);

namespace App\Domain\Order;

use App\Domain\Exception\OrderNotEditableException;
use App\Domain\Invoice\Invoice;
use App\Domain\Product\Product;
use App\Domain\ValueObject\Money;
use App\Domain\ValueObject\Quantity;

final class Order
{
    private OrderStatus $status;

    /** @var OrderItem[] */
    private array $items = [];

    /** @var Invoice[] */
    private array $invoices = [];

    private function __construct()
    {
        $this->status = OrderStatus::NEW;
    }

    public static function create(): self
    {
        return new self();
    }

    public function addProduct(Product $product, Quantity $quantity): void
    {
        if (!$this->status->allowsEditing()) {
            throw new OrderNotEditableException($this->status);
        }

        $this->items[] = OrderItem::create($product, $quantity);
    }

    public function issueInvoice(): Invoice
    {
        $this->cancelAllActiveInvoices();

        $invoice = Invoice::createForOrder($this);
        $this->invoices[] = $invoice;

        $this->status = OrderStatus::INVOICED;

        return $invoice;
    }

    public function markAsPaid(): void
    {
        $this->status = OrderStatus::PAID;
    }

    public function calculateTotalAmount(): Money
    {
        if (empty($this->items)) {
            return Money::fromAmount(0);
        }

        $total = Money::fromAmount(0);
        foreach ($this->items as $item) {
            $total = $total->add($item->getTotalPrice());
        }

        return $total;
    }

    public function getStatus(): OrderStatus
    {
        return $this->status;
    }

    /** @return OrderItem[] */
    public function getItems(): array
    {
        return $this->items;
    }

    /** @return Invoice[] */
    public function getInvoices(): array
    {
        return $this->invoices;
    }

    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    private function cancelAllActiveInvoices(): void
    {
        foreach ($this->invoices as $invoice) {
            if ($invoice->isNew()) {
                $invoice->cancel();
            }
        }
    }
}