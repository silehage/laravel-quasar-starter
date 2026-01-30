<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cruds = ['View','Create', 'Update', 'Delete'];

        foreach(Permission::MODULES as $module) {

            foreach($cruds as $c) {
                $name = $c . ' ' . $module;
                $slug = Str::slug($name);
                if(Permission::where('slug', $name)->count() == 0) {
                    Permission::create([
                        'name' => $name,
                        'slug' => $slug,
                        'tag' => $module
                    ]);
                }
            }
        }
    }
}
