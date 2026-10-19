<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class ProductLine extends Model
{
    use HasUlids;

    protected $fillable = [
        'id',
        'product_id',
        'sku_number',
        'name_override',
        'variant_override',
        'base_price',
        'retail_price',
        'weight',
        'is_mp_product',
        'is_unl_stock',
    ];

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $timestamps = false;

    public $appends = ['name'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function name(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->name_override
        );
    }
}
