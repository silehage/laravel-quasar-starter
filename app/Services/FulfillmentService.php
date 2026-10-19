<?php

namespace App\Services;

use App\Models\Fulfillment;

class FulfillmentService
{
    public function index($request)
    {
        return Fulfillment::paginate($request->per_page ?? 10)->withQueryString();
    }
    public function processEvent($data)
    {
        Fulfillment::updateOrCreate([
            'id' => $data['id']
        ], $data);
    }
}
