<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class Fulfillment extends Model
{
    use HasUlids;
    
    protected $fillable = [
        'id',
        'name',
        'phone',
        'location',
        'company_id',
        'admin_id',
    ];

    public $timestamps = false;

}
