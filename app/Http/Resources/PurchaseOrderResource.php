<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'reference' => $this->reference,
            'description' => $this->description,
            'fulfillment' => $this->fulfillment ?? '',
            'etd_finished_at' => $this->etd_finished_at,
            'item_pending_count' => $this->item_pending_count,
            'item_processed_count' => $this->item_processed_count,
            'item_shipped_count' => $this->item_shipped_count,
            'item_completed_count' => $this->item_completed_count,
            'item_cancelled_count' => $this->item_cancelled_count,
            // Pluck langsung status dari relasi
            'status_list' => $this->items->pluck('status')->unique()->values()->toArray(),
        ];
    }
}
