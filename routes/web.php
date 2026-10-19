<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\ManufacturerController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\RevisionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\WorkflowController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('dashboard');
})->name('home');

Route::group([
    'middleware' => 'auth',
    'prefix' => 'app'
], function () {

    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::resource('accounts', AccountController::class);
    Route::resource('roles', RoleController::class);
    Route::get('permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::post('permissions/toggle', [PermissionController::class, 'toggle'])->name('permissions.toggle');

    Route::get('productions/readyToShip', [ProductionController::class, 'readyToShip'])->name('productions.readyToShip');
    Route::get('productions/scanCurrentProcess', [ProductionController::class, 'scanCurrentProcess'])->name('productions.scanCurrentProcess');
    Route::post('productions/completeCurrentProcess', [ProductionController::class, 'completeCurrentProcess'])->name('productions.completeCurrentProcess');
    Route::get('productions/currentProcess', [ProductionController::class, 'currentProcess'])->name('productions.currentProcess');
    Route::get('productions/waitingList', [ProductionController::class, 'waitingList'])->name('productions.waitingList');
    Route::get('productions/revisionList', [ProductionController::class, 'revisionList'])->name('productions.revisionList');
    Route::post('productions/assignWorker', [ProductionController::class, 'assignWorker'])->name('productions.assignWorker');

    Route::get('revision/revisionList', [RevisionController::class, 'revisionList'])->name('revision.list');
    Route::post('revision/internalRevision', [RevisionController::class, 'internalRevision'])->name('revision.internalRevision');
    Route::post('revision/rollbackRevision', [RevisionController::class, 'rollbackRevision'])->name('revision.rollbackRevision');

    Route::post('manufacturer/{id}/set', [ManufacturerController::class, 'setCurrent'])->name('manufacturer.setCurrent');

    Route::get('purchase-orders/workflows/{lineItemId}', [PurchaseOrderController::class, 'workflows'])->name('purchase-orders.workflows');
    Route::get('purchase-orders/report', [PurchaseOrderController::class, 'report'])->name('purchase-orders.report');
    Route::get('purchase-orders/handling/{workflowId}', [PurchaseOrderController::class, 'handling'])->name('purchase-orders.handling');
    Route::get('purchase-orders/detail', [PurchaseOrderController::class, 'detail'])->name('purchase-orders.detail');
    Route::resource('purchase-orders', PurchaseOrderController::class);

    Route::post('products/{id}/updateWorkflow', [ProductController::class, 'updateWorkflow'])->name('products.updateWorkflow');
    Route::resource('products', ProductController::class);
    Route::post('workflows/updateItems', [WorkflowController::class, 'updateItems'])->name('workflows.updateItems');
    Route::resource('workflows', WorkflowController::class);

    Route::get('master/manufacturer', [MasterDataController::class, 'manufacturer'])->name('master.manufacturer');
    Route::get('master/factories', [MasterDataController::class, 'factories'])->name('master.factories');
    Route::get('master/products', [MasterDataController::class, 'products'])->name('master.products');
    Route::get('master/productLines', [MasterDataController::class, 'productLines'])->name('master.productLines');
    Route::get('master/fulfillments', [MasterDataController::class, 'fulfillments'])->name('master.fulfillments');
    Route::get('master/productCategories', [MasterDataController::class, 'productCategories'])->name('master.productCategories');
});

require __DIR__ . '/auth.php';
