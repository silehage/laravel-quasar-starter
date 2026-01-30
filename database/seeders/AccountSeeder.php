<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::create([
            'name' => 'Super Admin',
            'is_default' => 1
        ]);

        User::where('id', 1)->update(['role_id' => $role->id]);

        Role::create(['name' => 'Admin']);

    }
}
