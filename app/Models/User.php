<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasUlids;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    public static function permissions()
    {
        $role = Auth::check() ? Auth::user()->role : null;

        if ($role) {
            if ($role->slug == 'super-admin') {
                return ['can-all'];
            }
            return Cache::remember('user_permissions_' . $role->id, now()->addMinute(), function () use ($role) {
                $data = DB::table('permissions')
                    ->select('permissions.slug')
                    ->join('permission_role', 'permission_role.permission_id', 'permissions.id')
                    ->where('permission_role.role_id', $role->id)
                    ->get()->pluck('slug')->toArray();

                return $data;
            });
        }

        return [];
    }
}
