<?php

namespace App\Models;

use App\Enums\LineItemStatusEnum;
use App\Enums\WorkflowStatusEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class LineItem extends Model
{
    use HasUlids;

    protected $fillable = [
        'product_line_id',
        'purchase_order_item_id',
        'workflow_id',
        'barcode',
        'status',
        'workflow_status',
        'etd_finished_at',
        'finished_at',
        'production_cost',
        'material_cost',
        'is_revision',
    ];

    protected $casts = [
        'status' => LineItemStatusEnum::class,
        'workflow_status' => WorkflowStatusEnum::class
    ];

    public $timestamps = false;

    public $appends = [
        'status_label',
        'status_color',
        'state_label',
        'state_color',
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
    public function stateLabel(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->workflow_status->label()
        );
    }
    public function stateColor(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->workflow_status->color()
        );
    }

    public function currentWorkflow()
    {
        return $this->belongsTo(Workflow::class, 'workflow_id');
    }

    public function assignments()
    {
        return $this->hasMany(LineItemWorkflow::class, 'line_item_id');
    }

    public function workflows()
    {
        return $this->hasMany(LineItemWorkflow::class);
    }
    public function productLine()
    {
        return $this->belongsTo(ProductLine::class);
    }
    public function latestWorkflow(): HasOne
    {
        return $this->hasOne(LineItemWorkflow::class)->latestOfMany();
    }

    protected static function booted()
    {
        static::creating(function ($model) {

            $productLineId = $model->product_line_id;
            $skuNumber = Cache::remember('skuByLineId' . $productLineId, now()->addHour(), function () use ($productLineId) {
                return DB::table('product_lines')->select('sku_number')->where('id', $productLineId)->value('sku_number');
            });

            $year = now()->format('Ym');
            $basePrefix = "{$skuNumber}{$year}";

            $last = static::where('barcode', 'like', $basePrefix . '%')
                ->latest('sort')
                ->select('barcode')
                ->value('barcode');

            $length = 5;

            if ($last) {
                $lastNumber = intval(substr($last, -$length));
                $next = str_pad($lastNumber + 1, $length, '0', STR_PAD_LEFT);
            } else {
                $next = str_pad(1, $length, '0', STR_PAD_LEFT);
            }

            $model->barcode =  $basePrefix . $next;
        });

        static::updated(function ($model) {
            // PurchaseOrderUpdateStatusJob::dispatch($model->purchase_order_item_id);
        });
    }
}
