<?php

namespace App\Http\Controllers;

use App\Enums\LineItemStatusEnum;
use App\Services\WorkflowService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductionController extends Controller
{
    public function __construct(
        protected WorkflowService $workflow
    ) {}
    public function waitingList(Request $request)
    {

        $result = $this->workflow->waitingList($request);
        return Inertia::render('Production/WaitingList', $result);
    }

    public function assignWorker(Request $request)
    {
        try {
            $request->validate([
                'worker_id' => ['required'],
                'items' => ['required', 'array'],
            ]);

            $this->workflow->assignWorkerFromRequest($request);
            return Inertia::flash('message', 'Berhasil assign worker')->back();
        } catch (\Throwable $th) {
            return Inertia::flash('error', $th->getMessage())->back();
        }
    }

    public function currentProcess(Request $request)
    {
        $result = $this->workflow->currentProcess($request);

        return Inertia::render('Production/Progress', $result);
    }
    public function completeCurrentProcess(Request $request)
    {
        try {
            $request->validate([
                'barcode' => ['required', 'exists:line_items,barcode']
            ]);
            $this->workflow->completeCurrentProcess($request->barcode);
            return Inertia::flash('message', 'Beehasil input data')->back();
        } catch (\Throwable $th) {
            return Inertia::flash('error', $th->getMessage())->back();
        }
    }
    public function scanCurrentProcess()
    {
        return Inertia::render('Production/ScanCurrentProcess');
    }
    public function readyToShip(Request $request)
    {
        return Inertia::render('Production/ReadyToShip', [
            'data' => $this->workflow->readyToShip($request),
            'statuses' => LineItemStatusEnum::productionOptions()
        ]);
    }
}
