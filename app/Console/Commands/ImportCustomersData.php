<?php

namespace App\Console\Commands;

use App\Models\Customer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Pharmacy;
use App\Models\Mask;
use App\Models\Order;
use App\Models\OrderDetail;

class ImportCustomersData extends Command
{
    protected $signature = 'import:customers-data';
    protected $description = 'Import customers and purchase histories data from json file';

    public function handle()
    {
        if (!Storage::disk('local')->exists('data/users.json')) {
            $this->error('User json file not found.');
            return ;
        }

        $data = json_decode(Storage::get('data/users.json'), true);
        
        DB::beginTransaction();

        try {
            foreach ($data as $customerData) {
                $customer = Customer::updateOrCreate(
                    ['name' => $customerData['name']],
                    ['cash_balance' => $customerData['cashBalance'] ?? 0]
                );

                foreach ($customerData['purchaseHistories'] as $purchase) {
                    $pharmacy = Pharmacy::where('name', $purchase['pharmacyName'])->first();
                    if (!$pharmacy) {
                        $this->warn("Pharmacy {$purchase['pharmacyName']} not found, skipping...");
                        continue;
                    }

                    $mask = Mask::where('pharmacy_id', $pharmacy->id)
                                ->where('name', $purchase['maskName'])
                                ->first();
                    if (!$mask) {
                        $this->warn("Mask {$purchase['maskName']} at pharmacy {$pharmacy->name} not found, skipping...");
                        continue;
                    }

                    $order = Order::firstOrCreate([
                        'customer_id' => $customer->id,
                        'purchased_at' => $purchase['transactionDatetime'],
                    ], [
                        'total_amount' => 0,
                    ]);

                    $totalPrice = $purchase['transactionAmount'];

                    OrderDetail::create([
                        'order_id' => $order->id,
                        'pharmacy_id' => $pharmacy->id,
                        'mask_id' => $mask->id,
                        'quantity' => $purchase['transactionQuantity'],
                        'unit_price' => $totalPrice / max($purchase['transactionQuantity'], 1),
                        'total_price' => $totalPrice,
                    ]);

                    $order->total_amount += $totalPrice;
                    $order->save();
                }
            }

            DB::commit();
            $this->info('Customers and purchase data imported successfully.');
            return;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Error importing data: ' . $e->getMessage());
            return;
        }
    }
}
