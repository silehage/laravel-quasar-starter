<?php

namespace App\Services;

use App\Enums\LineItemStatusEnum;
use App\Enums\WorkflowStatusEnum;
use App\Models\LineItem;
use App\Models\LineItemWorkflow;
use App\Models\Workflow;
use App\Models\WorkflowCategory;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class RevisionService
{
    public function revisionList($request)
    {
        $query = LineItem::with([
            'workflows',
            'productLine' // Untuk tahu nama baju / produknya apa
        ])
            ->where('workflow_status', WorkflowStatusEnum::PENDING)
            ->where('status', LineItemStatusEnum::REVISION);

        $data = $query->orderBy('etd_finished_at', 'asc')
            ->orderBy('workflow_id', 'asc')
            ->paginate($request->per_page ?? 15);

        $categories = WorkflowCategory::from('workflow_categories as wc')
            ->join('workflows as w', 'wc.id', 'w.workflow_category_id')
            ->leftJoin('line_items as li', 'li.workflow_id', 'w.id')
            ->select('wc.id', 'wc.name')
            ->selectRaw("COUNT(CASE WHEN li.workflow_status = 'pending' THEN li.id END) as tototal_counttal")
            ->groupBy('wc.id', 'wc.name')
            ->get();

        return [
            'data' => $data,
            'categories' => $categories,
        ];
    }

    public function flagRevision(string $barcode)
    {
        LineItem::where('barcode', $barcode)->update([
            'status' => LineItemStatusEnum::REVISION,
            'is_revision' => 1
        ]);

        return 1;
    }
    
    public function triggerInternalRevision(string $lineItemId, string $reason)
    {
        return DB::transaction(function () use ($lineItemId, $reason) {
            $lineItem = LineItem::findOrFail($lineItemId);

            // Validasi: Hanya bisa direvisi jika sedang dalam status 'processing' atau baru saja selesai tapi belum lanjut jauh
            if ($lineItem->workflow_status !== 'processing') {
                throw new Exception("Hanya item yang sedang diproses yang bisa direvisi internal.");
            }

            // Cari tugas yang sedang aktif berjalan
            $currentAssignment = LineItemWorkflow::where('line_item_id', $lineItem->id)
                ->where('workflow_id', $lineItem->workflow_id)
                ->whereNull('finished_at')
                ->first();

            if (!$currentAssignment) {
                throw new Exception("Data pekerjaan aktif tidak ditemukan.");
            }

            // Catat alasan revisi di deskripsi, restart waktu mulai, dan kosongkan finished_at jika terisi
            $currentAssignment->update([
                'desription' => "[REVISI INTERNAL] " . $reason,
                'start_at' => Carbon::now(), // reset waktu mulai perhitungan durasi kerja
            ]);

            // Status item dikembalikan ke 'processing' agar pekerja tahu harus memperbaiki
            $lineItem->update(['workflow_status' => 'processing']);

            return $lineItem;
        });
    }
    public function rollbackToPreviousWorkflow(string $lineItemId, string $targetWorkflowId, string $reason)
    {
        return DB::transaction(function () use ($lineItemId, $targetWorkflowId, $reason) {
            $lineItem = LineItem::with('currentWorkflow')->findOrFail($lineItemId);

            // Cari workflow target untuk memastikan kevalidannya
            $targetWorkflow = Workflow::findOrFail($targetWorkflowId);

            // Validasi: Pastikan workflow target memiliki urutan (sort) yang lebih kecil dan dalam kategori yang sama
            if ($targetWorkflow->workflow_category_id !== $lineItem->currentWorkflow->workflow_category_id) {
                throw new Exception("Workflow target harus berada dalam kategori produksi yang sama.");
            }
            
            if ($targetWorkflow->sort >= $lineItem->currentWorkflow->sort) {
                throw new Exception("Untuk melakukan revisi balik, urutan (sort) workflow target harus lebih rendah dari proses saat ini.");
            }

            // 1. Jika ada proses berjalan saat ini, tandai sebagai 'Dibatalkan/Direvisi'
            LineItemWorkflow::where('line_item_id', $lineItem->id)
                ->where('workflow_id', $lineItem->workflow_id)
                ->whereNull('finished_at')
                ->update([
                    'finished_at' => Carbon::now(),
                    'desription' => "[DIKEMBALIKAN/REVISI] " . $reason
                ]);

            // 2. Alihkan workflow_id ke proses sebelumnya
            $lineItem->workflow_id = $targetWorkflow->id;
            $lineItem->is_revision = 1;
            
            // 3. Set status ke 'pending' agar bisa dibagikan ulang ke pekerja sebelumnya (atau pekerja baru)
            $lineItem->workflow_status = 'processing';
            
            // Opsional: Jika revisi mundur terjadi saat statusnya sudah 'finished' (di QC akhir)
            if ($lineItem->workflow_status == 'finished') {
                $lineItem->finished_at = null; 
            }

            $lineItem->save();

            return $lineItem;
        });
    }
}