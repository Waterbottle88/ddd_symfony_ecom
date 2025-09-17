<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Order\Order;
use App\Domain\Order\OrderRepositoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final class InMemoryOrderRepository implements OrderRepositoryInterface
{
    private const SESSION_KEY_ORDERS = 'ecommerce_orders';
    private const SESSION_KEY_NEXT_ID = 'ecommerce_next_order_id';

    public function __construct(
        private RequestStack $requestStack
    ) {
    }

    public function save(Order $order): int
    {
        $orders = $this->getOrders();
        $nextId = $this->getNextId();

        $orders[$nextId] = $order;
        $this->setOrders($orders);
        $this->setNextId($nextId + 1);

        return $nextId;
    }

    public function findById(int $id): ?Order
    {
        $orders = $this->getOrders();
        return $orders[$id] ?? null;
    }

    /** @return Order[] */
    public function findAll(): array
    {
        return array_values($this->getOrders());
    }

    /** @return array<int, Order> */
    public function findAllWithIds(): array
    {
        return $this->getOrders();
    }

    public function clear(): void
    {
        $this->setOrders([]);
        $this->setNextId(1);
    }

    /** @return array<int, Order> */
    private function getOrders(): array
    {
        $session = $this->requestStack->getSession();
        return $session->get(self::SESSION_KEY_ORDERS, []);
    }

    /** @param array<int, Order> $orders */
    private function setOrders(array $orders): void
    {
        $session = $this->requestStack->getSession();
        $session->set(self::SESSION_KEY_ORDERS, $orders);
    }

    private function getNextId(): int
    {
        $session = $this->requestStack->getSession();
        return $session->get(self::SESSION_KEY_NEXT_ID, 1);
    }

    private function setNextId(int $nextId): void
    {
        $session = $this->requestStack->getSession();
        $session->set(self::SESSION_KEY_NEXT_ID, $nextId);
    }
}