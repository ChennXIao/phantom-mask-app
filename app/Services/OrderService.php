<?php

namespace App\Services;

use App\Repositories\OrderRepository;

class OrderService
{
    protected $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function getTopSpenders(array $data)
    {
        return $this->orderRepository->getTopSpendersData(
            $data['start_date'],
            $data['end_date'],
            $data['limit'] ?? 5
        );
    }

    public function createOrder(array $data)
    {
        return $this->orderRepository->createOrderWithDetails($data);
    }
}
