<?php

namespace App\Services;

use App\Models\Factory;
use App\Models\Manufacturer;

class ManufacturerService
{
    public function index($req)
    {
        return Manufacturer::paginate($req->per_page ?? 10);
    }
    public function getDefault()
    {
        
    }
    public function factories($req)
    {
        return Factory::paginate($req->per_page ?? 10);
    }
    public function processEvent($data)
    {
        Manufacturer::updateOrCreate([
            'id' => $data['id']
        ], $data);
    }
    public function processFactoryEvent($data)
    {
        Factory::updateOrCreate([
            'id' => $data['id']
        ], $data);
    }
}