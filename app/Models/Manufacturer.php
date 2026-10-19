<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class Manufacturer extends Model
{
    use HasUlids;
    
    protected $fillable = [
        'id',
        'name',
        'phone',
        'email',
        'address',
    ];

    public $timestamps = false;

    public function factories()
    {
        return $this->hasMany(Factory::class);
    }
}
