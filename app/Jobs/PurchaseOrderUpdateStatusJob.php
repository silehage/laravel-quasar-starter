<?php

namespace App\Jobs;

use App\Services\PurchaseOrderService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class PurchaseOrderUpdateStatusJob implements ShouldQueue
{
    use Queueable;

    public string $purchaseOrderItemId;

    /**
     * Create a new job instance.
     */
    public function __construct(string $purchaseOrderItemId)
    {
        $this->purchaseOrderItemId = $purchaseOrderItemId;
    }

    /**
     * Execute the job.
     */
    public function handle(PurchaseOrderService $service): void
    {
        $service->updatingStatus($this->purchaseOrderItemId);
    }
}
