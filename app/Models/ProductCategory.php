<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use HasUlids;
    
     protected $fillable = [
        'id',
        'name',
    ];

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $timestamps = false;
}
