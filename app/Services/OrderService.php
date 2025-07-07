<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Mask;
use App\Models\Customer;
use App\Models\Pharmacy;
use App\Repositories\OrderRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

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
