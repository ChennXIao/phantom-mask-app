<?php

namespace App\Services;

use App\Exceptions\InsufficientStockException;
use App\Exceptions\MaskNotFoundInPharmacyException;
use App\Models\Pharmacy;
use App\Models\Mask;
use App\Repositories\PharmacyRepository;
use Illuminate\Http\Request;

class PharmacyService
{
    protected $pharmacyRepository;

    public function __construct(PharmacyRepository $pharmacyRepository)
    {
        $this->pharmacyRepository = $pharmacyRepository;
    }

    public function getAllPharmacies($data)
    {
        $day = isset($data['day']) ? $data['day'] : '';
        $time = isset($data['time']) ? $data['time'] : '';

        return $this->pharmacyRepository->getPharmaciesByDayAndTime($day, $time)
            ->map(function ($pharmacy) {
                return [
                    'id' => $pharmacy->id,
                    'name' => $pharmacy->name,
                    'hours' => $pharmacy->hours->map(function ($hours) {
                        return collect($hours)->only(['weekday', 'open_time', 'close_time']);
                    })->values(),
                ];
            })
            ->values();
    }

    public function getMasksForPharmacy(Pharmacy $pharmacy, array $validatedData)
    {
        $sort = $validatedData['sort'] ?? null;
        $order = $validatedData['order'] ?? 'asc';

        return $this->pharmacyRepository->getMasks($pharmacy, $sort, $order)
            ->map(function ($mask) {
                return [
                    'id' => $mask->id,
                    'name' => $mask->name,
                    'price' => $mask->price,
                    'stock_quantity' => $mask->stock_quantity,


                ];
            })
            ->values();
    }

    public function filterPharmaciesByMaskCount(array $validatedData)
    {
        $minPrice = $validatedData['min_price'] ?? null;
        $maxPrice = $validatedData['max_price'] ?? null;
        $minCount = $validatedData['min_count'] ?? null;
        $maxCount = $validatedData['max_count'] ?? null;

        return $this->pharmacyRepository->filterByMaskCount($minPrice, $maxPrice, $minCount, $maxCount)
            ->filter(function ($pharmacy) {
                return $pharmacy->masks->isNotEmpty();
            })
            ->map(function ($pharmacy) {
                return [
                    'id' => $pharmacy->id,
                    'name' => $pharmacy->name,
                    'masks' => $pharmacy->masks->map(function ($mask) {
                        return collect($mask)->only(['id', 'name', 'price', 'stock_quantity']);
                    })->values(),
                ];
            })
            ->values();
    }

    public function batchUpsertMasks(Pharmacy $pharmacy, array $masksData)
    {
        $results = [];

        foreach ($masksData as $maskData) {
            $updatedMask = $this->pharmacyRepository->upsertMask($pharmacy, $maskData);
            $results[] = $updatedMask;
        }

        return collect($results)->map(function ($mask) {

            return collect($mask)->except(['is_active', 'created_at', 'updated_at', 'pharmacy_id']);
        })->values();
    }

    public function updateMaskStock(Pharmacy $pharmacy, Mask $mask, int $stockDelta)
    {
        if (!$this->pharmacyRepository->isMaskBelongsToPharmacy($mask, $pharmacy->id)) {
            throw new MaskNotFoundInPharmacyException("Mask id {$mask->id} not found in the specified pharmacy.");
        }

        $newStock = $this->pharmacyRepository->getStockQuantity($mask) + $stockDelta;

        if ($newStock < 0) {
            throw new InsufficientStockException();
        }

        $mask =  $this->pharmacyRepository->updateStock($mask, $stockDelta);
        return [
            'id' => $mask->id,
            'name' => $mask->name,
            'price' => $mask->price,
            'stock_quantity' => $mask->stock_quantity,
        ];
    }

    public function searchPharmaciesAndMasks(array $validatedData)
    {
        return $this->pharmacyRepository->search($validatedData['q']);
    }
}
