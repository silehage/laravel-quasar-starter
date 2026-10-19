<?php

namespace App\Http\Controllers;

use App\DTOs\WorkflowDTO;
use App\Services\ProductService;
use App\Services\WorkflowCategoryService;
use App\Services\WorkflowService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WorkflowController extends Controller
{
    public function __construct(
        protected WorkflowService $workflow,
        protected WorkflowCategoryService $category,
        protected ProductService $product,
    ) {}
    public function index()
    {
        return Inertia::render('Workflow/Index', [
            'categories' => $this->category->listOptions(),
            'data' => $this->workflow->list()
        ]);
    }
    public function create()
    {
        return Inertia::render('Workflow/Form', [
            'categories' => $this->category->listOptions(),
        ]);
    }
    public function edit($id)
    {
        return Inertia::render('Workflow/Form', [
            'data' => $this->category->show($id),
            'categories' => $this->category->listOptions(),
        ]);
    }
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => ['required'],
            'name' => ['required', 'string'],
            'default_cost' => ['required', 'numeric', 'min:0'],
        ]);

        $this->workflow->store(
            new WorkflowDTO(
                categoryId: $request->category_id,
                name: $request->name,
                cost: $request->default_cost,
                sort: $request->sort ?? 1
            )
        );
        return Inertia::flash('message', 'Berhasil tambah data')->back();
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'category_id' => ['required'],
            'name' => ['required', 'string'],
            'default_cost' => ['required', 'numeric', 'min:0'],
        ]);

        $this->workflow->update(
            $id,
            new WorkflowDTO(
                categoryId: $request->category_id,
                name: $request->name,
                cost: $request->default_cost,
                sort: $request->sort ?? 1
            ),
        );

        return Inertia::flash('message', 'Berhasil update data')->back();
    }
    public function updateItems(Request $request)
    {
        $request->validate([
            'workflows' => ['required', 'array'],
            'workflows.*.id' => ['required'],
            'workflows.*.workflow_category_id' => ['required'],
            'workflows.*.name' => ['required'],
            'workflows.*.default_cost' => ['required', 'numeric', "min: 0"],
        ]);

        $this->workflow->updateItems($request);

        return Inertia::flash('message', 'Berhasil update data')->back();
    }
    public function destroy($id)
    {
        $this->workflow->destroy($id);

        return Inertia::flash('message', 'Berhasil hapus data')->back();
    }
}
