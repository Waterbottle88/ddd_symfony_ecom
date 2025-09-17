<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Application\DTO\AddProductToOrderRequest;
use App\Domain\Order\Order;
use App\Domain\Order\OrderRepositoryInterface;
use App\Domain\Product\Product;
use App\Domain\ValueObject\Quantity;

final class OrderService
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository
    ) {
    }

    public function createOrder(): int
    {
        $order = Order::create();
        return $this->orderRepository->save($order);
    }

    public function addProductToOrder(int $orderId, Product $product, float $quantity): void
    {
        $order = $this->orderRepository->findById($orderId);
        if (!$order) {
            throw new \InvalidArgumentException('Order not found');
        }

        $quantityValue = Quantity::fromFloat($quantity);
        $order->addProduct($product, $quantityValue);
    }

    public function addProductToOrderFromRequest(AddProductToOrderRequest $request, Product $product): void
    {
        $this->addProductToOrder($request->orderId, $product, $request->quantity);
    }

    public function getOrder(int $orderId): ?Order
    {
        return $this->orderRepository->findById($orderId);
    }

    /** @return Order[] */
    public function getAllOrders(): array
    {
        return $this->orderRepository->findAll();
    }

    /** @return array<int, Order> */
    public function getAllOrdersWithIds(): array
    {
        return $this->orderRepository->findAllWithIds();
    }
}