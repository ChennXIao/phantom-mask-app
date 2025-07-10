<?php

namespace App\Repositories;

use App\Models\Pharmacy;
use App\Models\Mask;

class PharmacyRepository
{
    public function getPharmaciesByDayAndTime(?string $day, ?string $time = '')
    {
        $query = Pharmacy::query();

        if ($day || $time) {
            $query->whereHas('hours', function ($q) use ($day, $time) {
                $q->where('weekday', $day)
                    ->where('open_time', '<=', $time)
                    ->where('close_time', '>=', $time);
            });
        }

        return $query->with('hours')->get();
    }

    public function getMasks(Pharmacy $pharmacy, ?string $sort, string $order)
    {
        $query = $pharmacy->masks();

        if ($sort) {
            $query->orderBy($sort, $order);
        }

        return $query->get();
    }

    public function filterByMaskCount(?float $minPrice = null, ?float $maxPrice = null, ?int $minCount = null, ?int $maxCount = null)
    {
        $query = Pharmacy::withCount([
            'masks as count' => function ($query) use ($minPrice, $maxPrice) {
                $query->whereBetween('price', [$minPrice, $maxPrice]);
            }
        ]);

        if (!is_null($minCount) && !is_null($maxCount)) {
            $query->havingBetween('masks_in_range_count', [$minCount, $maxCount]);
        } elseif (!is_null($minCount)) {
            $query->having('count', '>=', $minCount);
        } elseif (!is_null($maxCount)) {
            $query->having('count', '<=', $maxCount);
        }

        return $query
            ->with(['masks' => function ($query) use ($minPrice, $maxPrice) {
                $query->whereBetween('price', [$minPrice, $maxPrice])
                    ->select('id', 'pharmacy_id', 'name', 'price');
            }])
            ->get();
    }

    public function upsertMask(Pharmacy $pharmacy, array $maskData)
    {
        return $pharmacy->masks()->updateOrCreate(
            ['name' => $maskData['name']],
            [
                'price' => $maskData['price'],
                'stock_quantity' => $maskData['stock_quantity'],
            ]
        );
    }

    public function updateStock(Mask $mask, int $stockDelta): Mask
    {
        $mask->stock_quantity += $stockDelta;
        $mask->save();

        return $mask;
    }


    public function isMaskBelongsToPharmacy(Mask $mask, int $pharmacyId): bool
    {
        return $mask->pharmacy_id === $pharmacyId;
    }

    public function getStockQuantity(Mask $mask): int
    {
        return $mask->stock_quantity;
    }

    public function search(string $term)
    {
        $pharmacies = Pharmacy::where('name', 'like', "%{$term}%")
            ->select('id', 'name')
            ->get();

        $masks = Mask::where('name', 'like', "%{$term}%")
            ->select('id', 'name', 'pharmacy_id')
            ->get();

        return [
            'pharmacies' => $pharmacies,
            'masks' => $masks,
        ];
    }
}
