<?php

namespace App\Services;

use App\Models\ProductCategory;

class ProductCategoryService
{
    public function index($request)
   {
      return ProductCategory::paginate($request->per_page ?? 10)->withQueryString();
   }
   public function processEvent(array $data)
   {
      ProductCategory::updateOrCreate([
         'id' => $data['id']
      ], $data);
   }
}
