<?php

namespace App\Http\Controllers;

use App\Http\Requests\BatchUpsertMasksRequest;
use App\Http\Requests\FilterByMaskCountRequest;
use App\Http\Requests\ListMasksRequest;
use App\Http\Requests\ListPharmaciesRequest;
use App\Http\Requests\SearchRequest;
use App\Http\Requests\UpdateMaskStockRequest;
use App\Models\Pharmacy;
use App\Models\Mask;
use App\Services\PharmacyService;

class PharmacyController extends Controller
{
    protected $pharmacyService;

    public function __construct(PharmacyService $pharmacyService)
    {
        $this->pharmacyService = $pharmacyService;
    }

    public function index(ListPharmaciesRequest $request)
    {
        $validatedData = $request->validated();

        $pharmacies = $this->pharmacyService->getAllPharmacies($validatedData);
        return response()->success($pharmacies);
    }

    public function masks(ListMasksRequest $request, Pharmacy $pharmacy)
    {
        $validatedData = $request->validated();

        $masks = $this->pharmacyService->getMasksForPharmacy($pharmacy, $validatedData);
        return response()->success($masks);
    }

    public function filterByMaskCount(FilterByMaskCountRequest $request)
    {
        $validatedData = $request->validated();

        $pharmacies = $this->pharmacyService->filterPharmaciesByMaskCount($validatedData);
        return response()->success($pharmacies);
    }
    
    public function batchUpsert(BatchUpsertMasksRequest $request, Pharmacy $pharmacy)
    {
        $validatedData = $request->validated();

        $this->pharmacyService->batchUpsertMasks($pharmacy, $validatedData['masks']);

        return response()->success();
    }

    public function updateMaskStock(UpdateMaskStockRequest $request, Pharmacy $pharmacy, Mask $mask)
    {
        $validated = $request->validated();

        $updatedMask = $this->pharmacyService->updateMaskStock($pharmacy, $mask, $validated['stock_delta']);
        return response()->success($updatedMask);
    }

    public function search(SearchRequest $request)
    {
        $validatedData = $request->validated();

        $results = $this->pharmacyService->searchPharmaciesAndMasks($validatedData);
        return response()->success($results);
    }
}
