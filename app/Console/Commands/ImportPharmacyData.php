<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\Pharmacy;
use App\Models\PharmacyHour;
use App\Models\Mask;

class ImportPharmacyData extends Command
{
    protected $signature = 'import:pharmacy-data';
    protected $description = 'Import pharmacy and mask data from pharmacy.json';

    public function handle()
    {
        if (!Storage::disk('local')->exists('data/pharmacies.json')) {
            $this->error('Pharmacy JSON file not found.');
            return;
        }

        $data = json_decode(Storage::get('data/pharmacies.json'), true);

        foreach ($data as $pharmacyData) {
            $pharmacy = Pharmacy::firstOrCreate([
                'name' => $pharmacyData['name'],
                'cash_balance' => $pharmacyData['cashBalance'],
            ]);

            $hoursList = explode(',', $pharmacyData['openingHours']);
            foreach ($hoursList as $hour) {
                $hour = trim($hour);

                if (preg_match('/(\w+)\s+(\d{2}:\d{2})\s*-\s*(\d{2}:\d{2})/', $hour, $matches)) {
                    $weekDay = $matches[1];
                    $openTime = $matches[2];
                    $closeTime = $matches[3];

                    PharmacyHour::create([
                        'pharmacy_id' => $pharmacy->id,
                        'weekday' => $weekMap[$weekDay] ?? 0,
                        'open_time' => $openTime,
                        'close_time' => $closeTime,
                    ]);
                } else {
                    $this->warn("Invalid opening hour format for: {$hour}");
                }
            }

            foreach ($pharmacyData['masks'] as $maskData) {
                Mask::create([
                    'pharmacy_id' => $pharmacy->id,
                    'name' => $maskData['name'],
                    'price' => $maskData['price'],
                    'stock_quantity' => $maskData['stockQuantity'],
                ]);
            }
        }

        $this->info('Pharmacy and mask data imported successfully.');
        return;
    }
}
