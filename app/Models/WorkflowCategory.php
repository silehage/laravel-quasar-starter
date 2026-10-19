<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class WorkflowCategory extends Model
{
    use HasUlids;
    
    protected $fillable = [
        'name'
    ];

    public $appends = ['label', 'value'];

    public function label(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->name
        );
    }
    public function value(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->id
        );
    }
    public function items()
    {
        return $this->hasMany(Workflow::class)->orderBy('sort');
    }
}
