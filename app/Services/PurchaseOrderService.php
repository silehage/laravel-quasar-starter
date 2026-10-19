<?php

namespace App\Services;

use App\DTOs\PurchaseOrderDTO;
use App\Enums\LineItemStatusEnum;
use App\Enums\PurchaseOrderItemStatusEnum;
use App\Enums\PurchaseOrderStatusEnum;
use App\Jobs\PurchaseOrderAutoProcessingJob;
use App\Models\LineItem;
use App\Models\LineItemWorkflow;
use App\Models\ProductLine;
use App\Models\ProductWorkflow;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Workflow;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PurchaseOrderService
{
    public function index($req)
    {
        return PurchaseOrder::leftJoin('fulfillments as fl', 'fl.id', 'purchase_orders.fulfillment_id')
            ->select(
                'purchase_orders.*',
                'fl.name as fulfillment',
            )
            ->with('items:id,purchase_order_id,status')
            ->withCount('itemPending')
            ->withCount('itemProcessed')
            ->withCount('itemShipped')
            ->withCount('itemRevision')
            ->withCount('itemCompleted')
            ->withCount('itemCancelled')
            ->paginate($req->per_page ?? 10);
    }
    public function show($id)
    {
        $purchaseOrder = PurchaseOrder::leftJoin('fulfillments as fl', 'fl.id', 'purchase_orders.fulfillment_id')
            ->select(
                'purchase_orders.*',
                'fl.name as fulfillment',
            )
            ->findOrFail($id);
        return [
            'data' => $purchaseOrder,
            'items' => $purchaseOrder->items
        ];
    }
    public function detail($request)
    {
        $instance = PurchaseOrderItem::from('purchase_order_items as poi');

        if ($request->categoryId && $request->categoryId != 'ALL') {

            if ($request->categoryId == 'UNCATEGORIZED') {

                $instance->whereNull('p.category_id');
            } else {

                $instance->where('p.category_id', $request->categoryId);
            }
        }

        if ($request->status && $request->status != 'ALL') {
            $instance->where('poi.status', $request->status);
        } else {
            $instance->whereNotIn('poi.status', [
                PurchaseOrderItemStatusEnum::COMPLETED->value,
                PurchaseOrderItemStatusEnum::CANCELLED->value,
            ]);
        }

        if ($request->factoryId) {
            $instance->where('po.factory_id', $request->factoryId);
        }

        if ($request->search) {
            $instance->where(function ($q) use ($request) {
                $q->where('po.reference', $request->search)
                    ->orWhere('pl.sku_number', $request->search)
                    ->orWhere('pl.name_override', 'like', $request->search . '%');
            });
        }

        $instance->join('purchase_orders as po', 'po.id', 'poi.purchase_order_id');
        $instance->join('product_lines as pl', 'pl.id', 'poi.product_line_id');
        $instance->join('products as p', 'p.id', 'pl.product_id');
        $instance->select(
            'pl.name_override as product_name',
            'p.manufacturer_id',
            'p.category_id',
            'poi.*',
            'po.reference as po_number',
            'po.factory_id',
            'po.etd_finished_at',
        );

        $totalQty = $instance->sum('poi.quantity');
        $partialQty = $instance->sum('poi.partial_qty');

        $data = [
            'progress' => [
                'partial_qty' => $partialQty,
                'total_qty' => $totalQty
            ],
            'data' => $instance->with(['lineItems' => function ($q) {
                $q->join('workflows', 'workflows.id', 'line_items.workflow_id')
                    ->select('line_items.*', 'workflows.name as workflow');
            }])->orderBy('poi.id', 'desc')
                ->paginate($$request->per_page ?? 10)->withQueryString()
        ];

        return $data;
    }
    public function handling($workflowId)
    {
        $data = LineItem::from('line_items as li')
            ->where('li.workflow_id', $workflowId)
            // ->where('po.factory_id', $factoryId)
            ->join('purchase_order_items as poi', 'poi.id', 'li.purchase_order_item_id')
            ->join('purchase_orders as po', 'po.id', 'poi.purchase_order_id')
            ->join('product_lines as pl', 'pl.id', 'poi.product_line_id')
            ->select(
                'pl.name_override as product_name',
                'li.*',
                'poi.reference as po_item_number',
                'po.reference as po_number',
                'po.factory_id',
            )
            ->orderBy('li.etd_finished_at', 'desc')
            ->paginate($request->per_page ?? 100);

        return $data;
    }
    public function workflows($lineItemId)
    {
        $data = LineItem::from('line_items as li')
            ->join('purchase_order_items as poi', 'poi.id', 'li.purchase_order_item_id')
            ->join('purchase_orders as po', 'po.id', 'poi.purchase_order_id')
            ->join('product_lines as pl', 'pl.id', 'poi.product_line_id')
            ->select(
                'pl.name_override as product_name',
                'li.*',
                'poi.reference as po_item_number',
                'po.reference as po_number',
                'po.factory_id',
            )
            ->where('li.id', $lineItemId)
            ->first();

        $data->items = LineItemWorkflow::from('line_item_workflows as liw')
            ->join('workflows as w', 'w.id', 'liw.workflow_id')
            ->leftJoin('users as u', 'u.id', 'liw.worker_id')
            ->select(
                'liw.*',
                'w.name as workflow',
                'u.name as worker'
            )
            ->where('liw.line_item_id', $lineItemId)
            ->get();

        return $data;
    }
    public function report($request)
    {
        $report = DB::table('workflows as w')
            ->leftJoin('line_items as li', 'w.id', 'li.workflow_id')
            ->leftJoin('workflow_categories as wc', 'wc.id', 'w.workflow_category_id')
            ->select(
                'w.id as workflow_id',
                'w.name as workflow_name',
                'wc.id as category_id',
                'wc.name as category_name',
            )
            ->selectRaw("COUNT(li.id) as total")
            ->groupBy('wc.id', 'wc.name', 'w.id', 'w.name')
            ->orderBy('w.sort')
            ->get();

        return $report->groupBy('category_name');
    }
    public function storeData(PurchaseOrderDTO $dto)
    {

        try {

            DB::beginTransaction();

            $model = PurchaseOrder::create([
                'procurement_id' => $dto->procurement_id,
                'description' => $dto->description,
                'fulfillment_id' => $dto->fulfillment_id,
                'manufacturer_id' => $dto->manufacturer_id,
                'factory_id' => $dto->factory_id,
                'etd_finished_at' => $dto->etd_finished_at,
                'status' => $dto->status
            ]);

            foreach ($dto->items as $dtoItem) {
                $line = [
                    'product_line_id' => $dtoItem->product_line_id,
                    'quantity' => $dtoItem->quantity,
                    'status' => $dtoItem->status
                ];
                $model->items()->create($line);
            }

            DB::commit();

            PurchaseOrderAutoProcessingJob::dispatchSync($model);

            return $model;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function generatingListItems(PurchaseOrder $purchaseOrder)
    {

        try {
            DB::transaction(function () use ($purchaseOrder) {
                foreach ($purchaseOrder->items as $poItem) {

                    for ($i = 0; $i < $poItem->quantity; $i++) {

                        $productLineId = $poItem->product_line_id;

                        $product = Cache::remember('produkbypl_' . $productLineId, now()->addMinutes(2), function () use ($productLineId) {
                            return ProductLine::join('products', 'products.id', 'product_lines.product_id')
                                ->where('product_lines.id', $productLineId)
                                ->select(
                                    'products.*'
                                )
                                ->firstOrFail();
                        });

                        if (!$product->workflow_category_id) {

                            throw new Exception("Workflow kategori untuk produk {$product->name} belum di setting");
                        }

                        $currentWorkflow = Workflow::where('workflow_category_id', $product->workflow_category_id)
                            ->orderBy('sort')
                            ->first();

                        if (!$currentWorkflow) {
                            throw new Exception("ProductLine: {$productLineId} belum di setting");
                        }

                        $lineItem = $poItem->lineItems()->create([
                            'product_line_id' => $poItem->product_line_id,
                            'etd_finished_at' => $purchaseOrder->etd_finished_at,
                            'workflow_id' => $currentWorkflow->id,
                            'status' => LineItemStatusEnum::PROCESSING,
                            'workflow_status' => 'pending'
                        ]);
                    }


                    $poItem->update(['status' => PurchaseOrderItemStatusEnum::PROCESSING]);
                }

                // $purchaseOrder->update(['status' => PurchaseOrderStatusEnum::PROCESSING]);
            });
        } catch (\Throwable $th) {
            Log::debug($th->getMessage());
            //  $purchaseOrder->update(['note' => $th->getMessage()]);
            //throw $th;
        }
    }

    public function getCategoryList($request = null)
    {
        $key = 'po-categories-' . http_build_query($request->all());

        Cache::forget($key);

        return Cache::remember($key, now()->addMinute(), function () use ($request) {
            $complete =  LineItemStatusEnum::COMPLETED->value;
            $cancel =  LineItemStatusEnum::CANCELLED->value;

            $instance = DB::table('product_categories as pc')
                ->leftJoin('products as p', 'pc.id', 'p.category_id')
                ->leftJoin('product_lines as pl', 'pl.product_id', 'p.id')
                ->leftJoin('purchase_order_items as poi', 'pl.id', 'poi.product_line_id')
                ->leftJoin('purchase_orders as po', 'po.id', 'poi.purchase_order_id');

            if ($request && $request->status && $request->status != 'ALL') {
                $state = $request->status;
                $instance->select(
                    'pc.id',
                    'pc.name as category',
                    DB::raw("SUM(CASE WHEN poi.status = '{$state}' THEN 1 ELSE 0 END) item_count"),
                );
            } else {

                $instance->select(
                    'pc.id',
                    'pc.name as category',
                    DB::raw("SUM(CASE WHEN poi.status != '{$complete}' 
                AND poi.status != '{$cancel}'
                THEN 1  ELSE 0 END) item_count"),
                );
            }

            if ($request->factoryId) {
                $instance->where('po.factory_id', $request->factoryId);
            }

            $items = $instance
                ->groupBy('pc.id', 'pc.name')
                ->get()->map(
                    function ($el) {
                        return [
                            'categoryId' => $el->id,
                            'categoryName' => $el->category ?? 'UNCATEGORIZED',
                            'itemCount' => $el->item_count
                        ];
                    }
                );

            $total = $items->sum('item_count');

            $items->prepend([
                'categoryName' => 'ALL',
                'itemCount' => $total
            ]);

            return $items;
        });
    }

    public function updatingStatus(string $purchaseOrderitemId)
    {

        $purchaseOrderItem = Cache::remember('po-item-' . $purchaseOrderitemId, now()->addMinutes(3), function () use ($purchaseOrderitemId) {
            return PurchaseOrderItem::findOrFail($purchaseOrderitemId);
        });

        $lineStatuses = LineItem::where('purchase_order_item_id', $purchaseOrderItem->id)
            ->select('status')
            ->get()
            ->pluck('status')
            ->toArray();

        if (count(array_unique($lineStatuses)) === 1) {
            $mainStatus = $lineStatuses[0];
        } else {
            $mainStatus = PurchaseOrderStatusEnum::PARTIAL;
        }

        DB::table('purchase_order_items')->where('id', $purchaseOrderItem->id)->update(['status' => $mainStatus]);

        $uniqueStatuses = PurchaseOrderItem::where('purchase_order_id', $purchaseOrderItem->purchase_order_id)
            ->select('status')
            ->get()
            ->pluck('status')
            ->toArray();

        if (count(array_unique($uniqueStatuses)) === 1) {
            $mainStatus = $uniqueStatuses[0];
        } else {
            $mainStatus = PurchaseOrderStatusEnum::PARTIAL;
        }

        DB::table('purchase_orders')->where('id', $purchaseOrderItem->purchase_order_id)->update(['status' => $mainStatus]);
    }
}
