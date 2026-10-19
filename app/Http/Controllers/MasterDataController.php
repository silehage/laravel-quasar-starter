<?php

namespace App\Http\Controllers;

use App\Services\FulfillmentService;
use App\Services\ManufacturerService;
use App\Services\ProductCategoryService;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MasterDataController extends Controller
{
    public function manufacturer(Request $request, ManufacturerService $service)
    {
        return Inertia::render('Master/Manufacturer', [
            'data' => $service->index($request)
        ]);
    }
    public function factories(Request $request, ManufacturerService $service)
    {
        return Inertia::render('Master/Factories', [
            'data' => $service->factories($request)
        ]);
    }
    public function fulfillments(Request $request, FulfillmentService $service)
    {
        return Inertia::render('Master/Fulfillments', [
            'data' => $service->index($request)
        ]);
    }
    public function productCategories(Request $request, ProductCategoryService $service)
    {
        return Inertia::render('Master/ProductCategories', [
            'data' => $service->index($request)
        ]);
    }
    public function products(Request $request, ProductService $service)
    {
        return Inertia::render('Master/Products', [
            'data' => $service->index($request)
        ]);
    }
    public function productLines(Request $request, ProductService $service)
    {
        return Inertia::render('Master/ProductLines', [
            'data' => $service->lines($request)
        ]);
    }
    public function stores(Request $request, ProductService $service)
    {
        return Inertia::render('Master/ProductLines', [
            'data' => $service->lines($request)
        ]);
    }
}
