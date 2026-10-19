<?php

namespace App\Jobs;

use App\Models\PurchaseOrder;
use App\Services\PurchaseOrderService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class PurchaseOrderAutoProcessingJob implements ShouldQueue
{
    use Queueable;

    public $purchaseorder;

    /**
     * Create a new job instance.
     */
    public function __construct(PurchaseOrder $purchaseorder)
    {
        $this->purchaseorder = $purchaseorder;
    }

    /**
     * Execute the job.
     */
    public function handle(PurchaseOrderService $service): void
    {
        $service->generatingListItems($this->purchaseorder);
    }
}
