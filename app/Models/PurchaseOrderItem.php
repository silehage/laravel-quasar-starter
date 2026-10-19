<?php

namespace App\Models;

use App\Enums\PurchaseOrderItemStatusEnum;
use App\Traits\UseReference;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PurchaseOrderItem extends Model
{
    use HasUlids, UseReference;

    protected $fillable = [
        'reference',
        'purchase_order_id',
        'product_line_id',
        'quantity',
        'partial_qty',
        'status',
    ];

    protected $casts = [
        'status' => PurchaseOrderItemStatusEnum::class
    ];

    public $appends = [
        'status_label',
        'status_color'
    ];

    public function statusLabel(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->status->label()
        );
    }
    public function statusColor(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->status->color()
        );
    }
    public function lineItems()
    {
        return $this->hasMany(LineItem::class);
    }

    protected static function booted()
    {
        // static::created(function ($model) {

        //     DB::afterCommit(function () use ($model) {});
        // });
    }
}
