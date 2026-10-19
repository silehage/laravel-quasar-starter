<?php

namespace Database\Seeders;

use App\DTOs\PurchaseOrderDTO;
use App\DTOs\PurchaseOrderItemDTO;
use App\Enums\PurchaseOrderItemStatusEnum;
use App\Enums\PurchaseOrderStatusEnum;
use App\Models\ProductLine;
use App\Models\PurchaseOrder;
use App\Services\PurchaseOrderService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PurchaseOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(PurchaseOrderService $service): void
    {
        PurchaseOrder::query()->delete();

        $lineItems = [];

        for ($i = 0; $i < 7; $i++) {
            $productLine = ProductLine::whereHas('product', function($q) {
                $q->whereNotNull('workflow_category_id');
            })->inRandomOrder()->first();

            $lineItems[] = new PurchaseOrderItemDTO(
                product_line_id: $productLine->id,
                quantity: rand(3, 10),
                status: PurchaseOrderItemStatusEnum::PENDING->value
            );
        }

        $dto = new PurchaseOrderDTO(
            procurement_id: '123',
            description: 'test',
            fulfillment_id: '01kt6789nwyncaj50n7z2yp8d8',
            manufacturer_id: '01ks7dszzp97mskng7284rz060',
            factory_id: '01ks7dt001zp2kf37fsyc9n6wz',
            etd_finished_at: now()->addDays(rand(7, 10)),
            status: PurchaseOrderStatusEnum::PENDING->value,
            items: $lineItems
        );

        $service->storeData($dto);

    }
}
