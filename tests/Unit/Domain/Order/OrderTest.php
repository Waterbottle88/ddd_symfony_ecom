<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Order;

use App\Domain\Exception\OrderNotEditableException;
use App\Domain\Order\Order;
use App\Domain\Order\OrderStatus;
use App\Domain\Product\Product;
use App\Domain\Product\ProductType;
use App\Domain\ValueObject\Money;
use App\Domain\ValueObject\ProductName;
use App\Domain\ValueObject\Quantity;
use PHPUnit\Framework\TestCase;

final class OrderTest extends TestCase
{
    public function testCanCreateOrder(): void
    {
        $order = Order::create();

        $this->assertEquals(OrderStatus::NEW, $order->getStatus());
        $this->assertTrue($order->isEmpty());
        $this->assertEmpty($order->getItems());
        $this->assertEmpty($order->getInvoices());
    }

    public function testCanAddProductToNewOrder(): void
    {
        $order = Order::create();
        $product = $this->createProduct('iPhone', 999.99, ProductType::PIECE);
        $quantity = Quantity::fromInt(2);

        $order->addProduct($product, $quantity);

        $this->assertFalse($order->isEmpty());
        $this->assertCount(1, $order->getItems());

        $orderItem = $order->getItems()[0];
        $this->assertEquals($product, $orderItem->getProduct());
        $this->assertEquals($quantity, $orderItem->getQuantity());
    }

    public function testCalculatesTotalAmountCorrectly(): void
    {
        $order = Order::create();

        $product1 = $this->createProduct('iPhone', 999.99, ProductType::PIECE);
        $product2 = $this->createProduct('Rice', 2.50, ProductType::WEIGHT);

        $order->addProduct($product1, Quantity::fromInt(1));
        $order->addProduct($product2, Quantity::fromFloat(2.0));

        $totalAmount = $order->calculateTotalAmount();

        $this->assertEquals(1004.99, $totalAmount->getAmountFloat());
    }

    public function testEmptyOrderHasZeroTotal(): void
    {
        $order = Order::create();

        $totalAmount = $order->calculateTotalAmount();

        $this->assertEquals(0.0, $totalAmount->getAmountFloat());
    }

    public function testCannotAddProductToInvoicedOrder(): void
    {
        $order = Order::create();
        $product = $this->createProduct('iPhone', 999.99, ProductType::PIECE);

        $order->addProduct($product, Quantity::fromInt(1));
        $order->issueInvoice();

        $this->expectException(OrderNotEditableException::class);
        $this->expectExceptionMessage('Order cannot be edited when status is "invoiced"');

        $order->addProduct($product, Quantity::fromInt(1));
    }

    public function testCannotAddProductToPaidOrder(): void
    {
        $order = Order::create();
        $product = $this->createProduct('iPhone', 999.99, ProductType::PIECE);

        $order->addProduct($product, Quantity::fromInt(1));
        $order->issueInvoice();
        $order->markAsPaid();

        $this->expectException(OrderNotEditableException::class);
        $this->expectExceptionMessage('Order cannot be edited when status is "paid"');

        $order->addProduct($product, Quantity::fromInt(1));
    }

    public function testIssueInvoiceChangesStatusToInvoiced(): void
    {
        $order = Order::create();
        $product = $this->createProduct('iPhone', 999.99, ProductType::PIECE);
        $order->addProduct($product, Quantity::fromInt(1));

        $invoice = $order->issueInvoice();

        $this->assertEquals(OrderStatus::INVOICED, $order->getStatus());
        $this->assertCount(1, $order->getInvoices());
        $this->assertSame($invoice, $order->getInvoices()[0]);
    }

    public function testIssueSecondInvoiceCancelsFirstInvoice(): void
    {
        $order = Order::create();
        $product = $this->createProduct('iPhone', 999.99, ProductType::PIECE);
        $order->addProduct($product, Quantity::fromInt(1));

        $firstInvoice = $order->issueInvoice();
        $secondInvoice = $order->issueInvoice();

        $this->assertCount(2, $order->getInvoices());
        $this->assertTrue($firstInvoice->isCancelled());
        $this->assertTrue($secondInvoice->isNew());
    }

    public function testMarkAsPaidChangesStatusToPaid(): void
    {
        $order = Order::create();
        $product = $this->createProduct('iPhone', 999.99, ProductType::PIECE);
        $order->addProduct($product, Quantity::fromInt(1));

        $order->markAsPaid();

        $this->assertEquals(OrderStatus::PAID, $order->getStatus());
    }

    private function createProduct(string $name, float $price, ProductType $type): Product
    {
        return Product::create(
            ProductName::fromString($name),
            Money::fromFloat($price),
            $type
        );
    }
}