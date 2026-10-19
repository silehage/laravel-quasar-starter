<?php

namespace App\Jobs;

use App\Services\ManufacturerService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class FactoryEventJob implements ShouldQueue
{
    use Queueable;

    public $payload;
    public function __construct($payload)
    {
        $this->payload = $payload;
    }
    public function handle(ManufacturerService $service): void
    {
        $service->processFactoryEvent($this->payload);
    }
}
