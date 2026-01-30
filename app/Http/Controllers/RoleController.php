<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Requests\RoleRequest;

class RoleController extends Controller
{
    public function index()
    {
        return Inertia::render('Account/RoleIndex', [
            'data' => Role::all()
        ]);
    }
    public function store(RoleRequest $request)
    {
        Role::create($request->validated());
        return to_route('roles.index')->withMessage('Berhasil tambah data');
    }
    public function update(RoleRequest $request, $id)
    {
        Role::find($id)->update($request->validated());
        return to_route('roles.index')->withMessage('Berhasil update data');
    }
}
