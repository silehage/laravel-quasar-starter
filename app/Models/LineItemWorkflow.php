<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LineItemWorkflow extends Model
{
    protected $fillable = [
        'line_item_id',
        'workflow_id',
        'worker_id',
        'desription',
        'cost',
        'start_at',
        'finished_at'
    ];

    public $timestamps = false;
    
    public function lineItem()
    {
        return $this->belongsTo(LineItem::class, 'line_item_id');
    }
    public function workflow()
    {
        return $this->belongsTo(Workflow::class, 'workflow_id');
    }
    public function worker()
    {
        return $this->belongsTo(User::class, 'worker_id');
    }
}
