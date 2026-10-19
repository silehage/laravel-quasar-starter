<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class Workflow extends Model
{
    use HasUlids;
    
    protected $fillable = [
        'workflow_category_id',
        'name',
        'default_cost',
        'sort',
    ];

    public $timestamps = false;

    public function category()
    {
        return $this->belongsTo(WorkflowCategory::class);
    }
}
