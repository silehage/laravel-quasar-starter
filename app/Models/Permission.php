<?php

namespace App\Models;

use App\Traits\HasSluggable;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasSluggable;
    protected $guarded = [];

    const MODULES = ['User','Role', 'Permission'];
}
