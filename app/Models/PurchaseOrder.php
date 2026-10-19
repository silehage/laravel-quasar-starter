<?php

namespace App\Models;

use App\Enums\PurchaseOrderStatusEnum;
use App\Traits\UseReference;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasUlids, UseReference;

    protected $fillable = [
        'reference',
        'procurement_id',
        'status',
        'description',
        'fulfillment_id',
        'manufacturer_id',
        'factory_id',
        'etd_finished_at',
    ];

    protected $casts = [
        'status' => PurchaseOrderStatusEnum::class
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

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }
    public function statusList()
    {
        return $this->hasMany(PurchaseOrderItem::class)->select('status');
    }
    public function itemsActive()
    {
        return $this->hasMany(PurchaseOrderItem::class)->whereNotIn('status', [PurchaseOrderStatusEnum::CANCELLED]);
    }
    public function itemPending()
    {
        return $this->hasMany(PurchaseOrderItem::class)->where('status', PurchaseOrderStatusEnum::PENDING);
    }
    public function itemProcessed()
    {
        return $this->hasMany(PurchaseOrderItem::class)->where('status', PurchaseOrderStatusEnum::PROCESSING);
    }
    public function itemShipped()
    {
        return $this->hasMany(PurchaseOrderItem::class)->where('status', PurchaseOrderStatusEnum::SHIPPED);
    }
    public function itemRevision()
    {
        return $this->hasMany(PurchaseOrderItem::class)->where('status', PurchaseOrderStatusEnum::REVISION);
    }
    public function itemCompleted()
    {
        return $this->hasMany(PurchaseOrderItem::class)->where('status', PurchaseOrderStatusEnum::COMPLETED);
    }
    public function itemCancelled()
    {
        return $this->hasMany(PurchaseOrderItem::class)->where('status', PurchaseOrderStatusEnum::CANCELLED);
    }
    public function factory()
    {
        return $this->belongsTo(Factory::class);
    }

    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class);
    }
}
