<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Mask;
use App\Models\Customer;
use App\Models\Pharmacy;
use Illuminate\Support\Facades\DB;

class OrderRepository
{
    public function create(array $data)
    {
        return Order::create($data);
    }

    public function update($id, array $data)
    {
        $order = Order::findOrFail($id);
        $order->update($data);

        return $order;
    }

    public function createOrderDetail(array $data)
    {
        return OrderDetail::create($data);
    }

    public function createOrderWithDetails(array $data)
    {
        return DB::transaction(function () use ($data) {
            $totalPrice = 0;
            $order = $this->create([
                'customer_id' => $data['customer_id'],
                'purchased_at' => now(),
                'total_amount' => 0,
            ]);

            foreach ($data['items'] as $item) {
                $mask = Mask::where('id', $item['mask_id'])
                    ->where('pharmacy_id', $item['pharmacy_id'])
                    ->lockForUpdate()
                    ->first();

                if (!$mask) {
                    throw new \App\Exceptions\MaskNotFoundInPharmacyException(
                        'Mask not found for ID ' .  $item['mask_id'] . ' in pharmacy ID ' . $item['pharmacy_id']
                    );
                }

                if ($mask->stock_quantity < $item['quantity']) {
                    throw new \App\Exceptions\InsufficientStockException(
                        'Insufficient stock for mask ID ' . $mask->id
                    );
                }

                $mask->stock_quantity -= $item['quantity'];
                $mask->save();

                $maskTotal = $mask->price * $item['quantity'];
                $totalPrice += $maskTotal;

                $pharmacyId = $mask->pharmacy_id;
                $pharmacy = Pharmacy::findOrFail($pharmacyId);
                $pharmacy->cash_balance += $maskTotal;
                $pharmacy->save();

                $this->createOrderDetail([
                    'order_id' => $order->id,
                    'mask_id' => $mask->id,
                    'pharmacy_id' => $pharmacyId,
                    'quantity' => $item['quantity'],
                    'unit_price' => $mask->price,
                    'total_price' => $maskTotal,
                ]);
            }

            $order = $this->update($order->id, ['total_amount' => $totalPrice]);

            $customer = Customer::findOrFail($data['customer_id']);
            
            if ($customer->cash_balance < $totalPrice) {
                throw new \App\Exceptions\InsufficientFundsException(
                    'Insufficient funds for customer ID ' . $data['customer_id']
                );
            }

            $customer->cash_balance -= $totalPrice;
            $customer->save();

            return $order;
        });
    }

    public function getTopSpendersData(string $startDate, string $endDate, int $limit)
    {
        return DB::table('orders')
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->whereBetween('purchased_at', [$startDate, $endDate])
            ->select('customers.name', DB::raw('SUM(total_amount) as total_spent'))
            ->groupBy('customers.id', 'customers.name')
            ->orderByDesc('total_spent')
            ->limit($limit)
            ->get();
    }
}
