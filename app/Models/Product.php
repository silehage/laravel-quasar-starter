<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasUlids;

    protected $fillable = [
        'id',
        'name',
        'slug',
        'code',
        'is_variant',
        'category_id',
        'manufacturer_id',
        'workflow_category_id',
    ];

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $timestamps = false;

    public function workflowCategory()
    {
        return $this->belongsTo(WorkflowCategory::class, 'workflow_category_id');
    }
    public function customWorkflowCosts()
    {
        return $this->hasMany(ProductWorkflowCost::class, 'product_id');
    }
}
