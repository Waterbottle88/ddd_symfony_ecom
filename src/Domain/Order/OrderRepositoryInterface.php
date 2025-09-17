<?php

declare(strict_types=1);

namespace App\Domain\Order;

interface OrderRepositoryInterface
{
    public function save(Order $order): int;

    public function findById(int $id): ?Order;

    /** @return Order[] */
    public function findAll(): array;

    /** @return array<int, Order> */
    public function findAllWithIds(): array;

    public function clear(): void;
}