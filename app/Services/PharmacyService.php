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
        $time = $data['time'] ?? null;

        return $this->pharmacyRepository->getPharmaciesByDayAndTime($day, $time);
    }

    public function getMasksForPharmacy(Pharmacy $pharmacy, array $validatedData)
    {
        $sort = $validatedData['sort'] ?? null;
        $order = $validatedData['order'] ?? 'asc';
        return $this->pharmacyRepository->getMasks($pharmacy, $sort, $order);
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
                unset($pharmacy->created_at, $pharmacy->updated_at);

                $pharmacy->masks = $pharmacy->masks->map(function ($mask) {
                    unset($mask->created_at, $mask->updated_at, $mask->pharmacy_id);
                    return $mask;
                });

                return $pharmacy;
            })
            ->values();
    }

    public function batchUpsertMasks(Pharmacy $pharmacy, array $masksData)
    {
        foreach ($masksData as $maskData) {
            $this->pharmacyRepository->upsertMask($pharmacy, $maskData);
        }
    }

    public function updateMaskStock(Pharmacy $pharmacy, Mask $mask, int $stockDelta): Mask
    {
        if (!$this->pharmacyRepository->isMaskBelongsToPharmacy($mask, $pharmacy->id)) {
            throw new MaskNotFoundInPharmacyException("Mask id {$mask->id} not found in the specified pharmacy");
        }

        $newStock = $this->pharmacyRepository->getStockQuantity($mask) + $stockDelta;

        if ($newStock < 0) {
            throw new InsufficientStockException();
        }

        return $this->pharmacyRepository->updateStock($mask, $stockDelta);
    }

    public function searchPharmaciesAndMasks(array $validatedData)
    {
        return $this->pharmacyRepository->search($validatedData['q']);
    }
}
