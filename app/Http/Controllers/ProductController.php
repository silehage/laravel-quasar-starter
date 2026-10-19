<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use App\Services\WorkflowCategoryService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $product,
        protected WorkflowCategoryService $workflowCategory
    )
    {
    }
    public function index(Request $request)
    {
        return Inertia::render('Products/Index', [
               'categories' => $this->workflowCategory->listOptions(),
               'data' => $this->product->index($request)
           ]);
    }
    public function show($id)
    {
        return Inertia::render('Products/Show', [
               'categories' => $this->workflowCategory->listOptions(),
               'data' => $this->product->show($id)
           ]);
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'workflow_category_id' => 'required'
        ]);

        $this->product->update($request, $id);

        return Inertia::flash('message', 'Berhasil Update Data')->back();

    }
    public function updateWorkflow(Request $request, $id)
    {
        $request->validate([
            'workflows' => ['required', 'array'],
            'workflows.*.id' => ['required'],
            'workflows.*.cost' => ['required'],
        ]);

        $this->product->updateWorkflow($request, $id);

        return Inertia::flash('message', 'Berhasil Update Data')->back();

    }
}
