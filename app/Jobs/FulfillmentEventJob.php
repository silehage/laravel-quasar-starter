<?php

namespace App\Jobs;

use App\Services\FulfillmentService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class FulfillmentEventJob implements ShouldQueue
{
    use Queueable;

   public $payload;
    public function __construct($payload)
    {
        $this->payload = $payload;
    }
    public function handle(FulfillmentService $service): void
    {
        $service->processEvent($this->payload);
    }
}
