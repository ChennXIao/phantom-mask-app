<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateOrderRequest;
use App\Http\Requests\TopSpendersRequest;
use App\Services\OrderService;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }
    
    public function topSpenders(TopSpendersRequest $request)
    {
        $validatedData = $request->validated();

        $topUsers = $this->orderService->getTopSpenders($validatedData);

        return response()->success($topUsers);
    }

    public function store(CreateOrderRequest $request)
    {
        $validatedData = $request->validated();

        $order = $this->orderService->createOrder($validatedData);
        return response()->success($order);
    }
}
