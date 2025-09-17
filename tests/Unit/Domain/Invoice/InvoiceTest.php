<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Invoice;

use App\Domain\Exception\InvoiceAlreadyPaidException;
use App\Domain\Exception\InvoiceCannotBePaidException;
use App\Domain\Exception\PaymentAmountMismatchException;
use App\Domain\Exception\PaymentFailedException;
use App\Domain\Invoice\Invoice;
use App\Domain\Invoice\InvoiceStatus;
use App\Domain\Order\Order;
use App\Domain\Order\OrderStatus;
use App\Domain\Product\Product;
use App\Domain\Product\ProductType;
use App\Domain\ValueObject\Money;
use App\Domain\ValueObject\ProductName;
use App\Domain\ValueObject\Quantity;
use PHPUnit\Framework\TestCase;

final class InvoiceTest extends TestCase
{
    public function testCanCreateInvoiceForOrder(): void
    {
        $order = $this->createOrderWithProduct();
        $invoice = Invoice::createForOrder($order);

        $this->assertEquals(InvoiceStatus::NEW, $invoice->getStatus());
        $this->assertTrue($invoice->isNew());
        $this->assertFalse($invoice->isPaid());
        $this->assertFalse($invoice->isCancelled());
        $this->assertSame($order, $invoice->getOrder());
        $this->assertEquals($order->calculateTotalAmount(), $invoice->getAmount());
    }

    public function testCanPayInvoiceWithCorrectAmount(): void
    {
        $order = $this->createOrderWithProduct();
        $invoice = Invoice::createForOrder($order);
        $paymentMethod = new MockPaymentMethod(true);

        $invoice->pay($paymentMethod);

        $this->assertTrue($invoice->isPaid());
        $this->assertEquals(OrderStatus::PAID, $order->getStatus());
    }

    public function testCannotPayInvoiceTwice(): void
    {
        $order = $this->createOrderWithProduct();
        $invoice = Invoice::createForOrder($order);
        $paymentMethod = new MockPaymentMethod(true);

        $invoice->pay($paymentMethod);

        $this->expectException(InvoiceAlreadyPaidException::class);
        $this->expectExceptionMessage('Invoice has already been paid and cannot be paid again');

        $invoice->pay($paymentMethod);
    }

    public function testCannotPayCancelledInvoice(): void
    {
        $order = $this->createOrderWithProduct();
        $invoice = Invoice::createForOrder($order);
        $paymentMethod = new MockPaymentMethod(true);

        $invoice->cancel();

        $this->expectException(InvoiceCannotBePaidException::class);
        $this->expectExceptionMessage('Invoice with status "cancelled" cannot be paid');

        $invoice->pay($paymentMethod);
    }

    public function testThrowsExceptionWhenPaymentFails(): void
    {
        $order = $this->createOrderWithProduct();
        $invoice = Invoice::createForOrder($order);
        $paymentMethod = new MockPaymentMethod(false, null, 'Insufficient funds');

        $this->expectException(PaymentFailedException::class);
        $this->expectExceptionMessage('Payment failed: Insufficient funds');

        $invoice->pay($paymentMethod);
    }

    public function testThrowsExceptionWhenPaymentAmountMismatch(): void
    {
        $order = $this->createOrderWithProduct();
        $invoice = Invoice::createForOrder($order);
        $wrongAmount = Money::fromFloat(500.00);
        $paymentMethod = new MockPaymentMethod(true, $wrongAmount);

        $this->expectException(PaymentAmountMismatchException::class);
        $this->expectExceptionMessage('Payment amount mismatch. Expected: 999.99 UAH, but received: 500.00 UAH');

        $invoice->pay($paymentMethod);
    }

    public function testCanCancelNewInvoice(): void
    {
        $order = $this->createOrderWithProduct();
        $invoice = Invoice::createForOrder($order);

        $invoice->cancel();

        $this->assertTrue($invoice->isCancelled());
    }

    public function testCannotCancelPaidInvoice(): void
    {
        $order = $this->createOrderWithProduct();
        $invoice = Invoice::createForOrder($order);
        $paymentMethod = new MockPaymentMethod(true);

        $invoice->pay($paymentMethod);
        $invoice->cancel();

        $this->assertTrue($invoice->isPaid());
        $this->assertFalse($invoice->isCancelled());
    }

    public function testInvoiceAmountEqualsOrderTotal(): void
    {
        $order = Order::create();

        $product1 = Product::create(
            ProductName::fromString('iPhone'),
            Money::fromFloat(999.99),
            ProductType::PIECE
        );

        $product2 = Product::create(
            ProductName::fromString('Rice'),
            Money::fromFloat(2.50),
            ProductType::WEIGHT
        );

        $order->addProduct($product1, Quantity::fromInt(1));
        $order->addProduct($product2, Quantity::fromFloat(2.0));

        $invoice = Invoice::createForOrder($order);

        $this->assertEquals(1004.99, $invoice->getAmount()->getAmountFloat());
    }

    private function createOrderWithProduct(): Order
    {
        $order = Order::create();
        $product = Product::create(
            ProductName::fromString('iPhone'),
            Money::fromFloat(999.99),
            ProductType::PIECE
        );
        $order->addProduct($product, Quantity::fromInt(1));

        return $order;
    }
}