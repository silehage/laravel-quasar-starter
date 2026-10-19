<?php

namespace App\Jobs;

use App\Services\ProductService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProductLineEventJob implements ShouldQueue
{
    use Queueable;

    public $payload;
    public function __construct($payload)
    {
        $this->payload = $payload;
    }
    public function handle(ProductService $service): void
    {
        $service->processProductLineEvent($this->payload);
    }
}
