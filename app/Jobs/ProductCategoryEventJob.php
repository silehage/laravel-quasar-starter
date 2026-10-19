<?php

namespace App\Jobs;

use App\Services\ProductCategoryService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProductCategoryEventJob implements ShouldQueue
{
    use Queueable;

    public $payload;
    public function __construct($payload)
    {
        $this->payload = $payload;
    }
    public function handle(ProductCategoryService $service): void
    {
        $service->processEvent($this->payload);
    }
}
