<?php

namespace App\Models;

use App\Traits\HasSluggable;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasSluggable;

    protected $guarded = [];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_role', 'role_id', 'permission_id');
    }
    public function users()
    {
        return $this->hasMany(User::class);
    }

}
