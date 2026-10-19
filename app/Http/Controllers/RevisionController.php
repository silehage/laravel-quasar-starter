<?php

namespace App\Http\Controllers;

use App\Services\RevisionService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RevisionController extends Controller
{
    public function __construct(
        protected RevisionService $revision
    ) {
    }
    public function revisionList(Request $request)
    {
        $result = $this->revision->revisionList($request);
        return Inertia::render('Production/RevisionList', $result);
    }
    public function internalRevision(Request $request)
    {
        $request->validate([
            'line_item_id' => 'required|exists:line_items,id',
            'reason'       => 'required|string|max:255',
        ]);

        try {
            $this->revision->triggerInternalRevision($request->line_item_id, $request->reason);
            return Inertia::flash('message', 'Revisi internal berhasil diajukan. Status pekerja di-reset.')->back();
        } catch (\Exception $e) {
             return Inertia::flash('error', $e->getMessage())->back($e->getCode());
        }
    }
    public function rollbackRevision(Request $request)
    {
        $request->validate([
            'line_item_id'       => 'required|exists:line_items,id',
            'target_workflow_id' => 'required|exists:workflows,id',
            'reason'             => 'required|string|max:255',
        ]);

        try {
            $this->revision->rollbackToPreviousWorkflow(
                $request->line_item_id,
                $request->target_workflow_id,
                $request->reason
            );
            return Inertia::flash(
                'message',
                'Item berhasil dikembalikan ke proses sebelumnya, Silakan bagikan ulang tugas ini ke pekerja pada tahapan tersebut.'
            )->back();
        } catch (\Exception $e) {
            return Inertia::flash('error', $e->getMessage())->back($e->getCode());
        }
    }
}
