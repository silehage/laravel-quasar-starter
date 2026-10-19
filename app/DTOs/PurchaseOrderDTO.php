<?php

namespace App\DTOs;

use App\Enums\PurchaseOrderItemStatusEnum;
use App\Enums\PurchaseOrderStatusEnum;
use Illuminate\Http\Request;

final class PurchaseOrderDTO
{

    public function __construct(
        readonly string $procurement_id,
        readonly string $description,
        readonly string $fulfillment_id,
        readonly string $manufacturer_id,
        readonly string $factory_id,
        readonly string $etd_finished_at,
        readonly string $status,
        readonly array $items
    ) {}

    public static function fromApiRequest(Request $request)
    {
        $lineItems = [];

        foreach($request->items as $item) {
            $lineItems[] = new PurchaseOrderItemDTO(
                product_line_id: $item['productLineId'],
                quantity: $item['quantity'],
                status: PurchaseOrderItemStatusEnum::PENDING->value
            );
        }
        return new self(
            procurement_id: $request->orderId,
            description: $request->description,
            fulfillment_id: $request->fulfillmentId,
            manufacturer_id: $request->manufacturerId,
            factory_id: $request->factoryId,
            etd_finished_at: $request->etd,
            status: PurchaseOrderStatusEnum::PENDING->value,
            items: $lineItems
        );
    }
}
