<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class Factory extends Model
{
    use HasUlids;
    
    protected $fillable = [
        'id',
        'name',
        'phone',
        'email',
        'address',
        'manufacturer_id'
    ];

    public $timestamps = false;
}
