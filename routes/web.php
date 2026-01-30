<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('dashboard');
})->name('home');

Route::group([
    'middleware' => 'auth',
    'prefix' => 'app'
    ], function() {

    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::resource('accounts', AccountController::class);
    Route::resource('roles', RoleController::class);
    Route::get('permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::post('permissions/toggle', [PermissionController::class, 'toggle'])->name('permissions.toggle');
});

require __DIR__ . '/auth.php';