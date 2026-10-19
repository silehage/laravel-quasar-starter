<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductLine;
use App\Models\ProductWorkflow;
use App\Models\Workflow;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductService
{
   public function __construct(
      protected WorkflowService $workflow
   ) {}
   public function getAll($request)
   {
      $hasWorkflow = $request->hasWorkflow ?? null;

      return Product::when(!empty($hasWorkflow), function ($q) use ($hasWorkflow) {
         if ($hasWorkflow == '1') {
            $q->whereNotNull('workflow_category_id');
         } else if ($hasWorkflow == '0') {
            $q->whereNull('workflow_category_id');
         }
      })->get();
   }
   public function index($request)
   {
      $hasWorkflow = $request->input('hasWorkflow');

      return Product::leftJoin('product_categories', 'product_categories.id', 'products.category_id')
         ->leftJoin('manufacturers', 'manufacturers.id', 'products.manufacturer_id')
         ->leftJoin('workflow_categories as wc', 'wc.id', 'products.workflow_category_id')
         ->select('products.*', 'product_categories.name as category_name', 'manufacturers.name as manufactur_name', 'wc.name as workflow_category')
         ->when(! is_null($hasWorkflow), function ($q) use ($hasWorkflow) {
            if ($hasWorkflow == '1') {
               $q->whereNotNull('workflow_category_id');
            } else if ($hasWorkflow == '0') {
               $q->whereNull('workflow_category_id');
            }
         })
         ->paginate($request->per_page ?? 10)->withQueryString();
   }
   public function find($id)
   {
      return Product::leftJoin('workflow_categories as wc', 'wc.id', 'products.workflow_category_id')
         ->select('products.*', 'wc.name as workflow_category')
         ->findOrFail($id);
   }
   public function show($id)
   {
      $data =  Product::leftJoin('workflow_categories as wc', 'wc.id', 'products.workflow_category_id')
         ->select('products.*', 'wc.name as workflow_category')
         ->findOrFail($id);

      $data->items = Workflow::where('product_workflows.product_id', $data->workflow_category_id)
         ->orderBy('sort')
         ->get();

      return $data;
   }
   public function update($request, $id)
   {
      try {
         return DB::transaction(function () use ($request, $id) {
            $product = Product::findOrFail($id);
            $product->update(['workflow_category_id' => $request->workflow_category_id]);
            return 1;
         });
      } catch (\Throwable $th) {
         Product::find($id)->update(['workflow_category_id' => null]);
         throw $th;
      }
   }
   public function lines($request)
   {
      return ProductLine::paginate($request->per_page ?? 10)->withQueryString();
   }
   public function search(string $keyword)
   {
      return Product::where('name', 'like', '%' . $keyword . '%')->get();
   }
   public function processProductEvent(array $data)
   {
      try {
         Product::updateOrCreate(
            ['id' => $data['id']],
            $data
         );
      } catch (\Throwable $th) {
         throw $th;
      }
   }
   public function processProductLineEvent(array $data)
   {
      try {
         ProductLine::updateOrCreate(
            ['id' => $data['id']],
            $data
         );
      } catch (\Throwable $th) {
         throw $th;
      }
   }
}
