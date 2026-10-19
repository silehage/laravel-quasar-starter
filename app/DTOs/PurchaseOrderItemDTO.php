<?php

namespace App\DTOs;

final class PurchaseOrderItemDTO
{
    public function __construct(
        readonly string $product_line_id,
        readonly int $quantity,
        readonly string $status,
    ) {}

}
