<?php

namespace App\Http\Controllers;

use App\Enums\PurchaseOrderItemStatusEnum;
use App\Http\Resources\PurchaseOrderResource;
use App\Models\Factory;
use App\Models\LineItem;
use App\Models\Workflow;
use App\Services\PurchaseOrderService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PurchaseOrderController extends Controller
{
    public function __construct(
        protected PurchaseOrderService $service
    ) {}
    public function index(Request $request)
    {
        return Inertia::render('PurchaseOrder/Index', [
            'data' => $this->service->index($request)
        ]);
    }
    public function show($id)
    {
        return Inertia::render('PurchaseOrder/Show', $this->service->show($id));
    }
    public function detail(Request $request)
    {
         return Inertia::render('PurchaseOrder/Detail', [
            'data' => $this->service->detail($request),
            'categories' => $this->service->getCategoryList($request),
            'statuses' => PurchaseOrderItemStatusEnum::options(),
            'factories' => Factory::all()
        ]);
    }
    public function report(Request $request)
    {
         return Inertia::render('PurchaseOrder/Report', [
            'data' => $this->service->report($request),
            'categories' => $this->service->getCategoryList($request),
            'statuses' => PurchaseOrderItemStatusEnum::options(),
            'factories' => Factory::all()
        ]);
    }
    public function handling($workflowId)
    {
         return Inertia::render('PurchaseOrder/Handling', [
            'data' => $this->service->handling($workflowId),
            'statuses' => PurchaseOrderItemStatusEnum::options(),
            'workflow' => Workflow::find($workflowId)
        ]);
    }
    public function workflows($lineItemId)
    {
         return Inertia::render('PurchaseOrder/Workflows', [
            'data' => $this->service->workflows($lineItemId),
            'statuses' => PurchaseOrderItemStatusEnum::options(),
        ]);
    }
}
