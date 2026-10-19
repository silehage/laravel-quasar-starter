<?php

namespace App\Services;

use App\Models\WorkflowCategory;

class WorkflowCategoryService
{
    public function index()
    {
        return WorkflowCategory::with('items')->get();
    }
    public function show($id)
    {
        return WorkflowCategory::with('items')->find($id);
    }
    public function listOptions()
    {
        return WorkflowCategory::select('id', 'name' ,'id as value', 'name as label')->get();
    }
}