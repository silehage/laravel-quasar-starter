<?php

namespace App\Http\Controllers\Api;

use App\DTOs\PurchaseOrderDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\PurchaseOrderRequest;
use App\Services\PurchaseOrderService;

class PurchaseOrderApiController extends Controller
{
    public function __construct(
        protected PurchaseOrderService $service
    ) {}
    public function store(PurchaseOrderRequest $request)
    {
        try {
            $data = $this->service->storeData(
                PurchaseOrderDTO::fromApiRequest($request)
            );
            return response()->json(['success' => true, 'message' => 'Create data successfully!', 'data' => $data]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()], 400);
        }
    }
}
