<?php

namespace App\Services;

use App\DTOs\WorkflowDTO;
use App\Enums\LineItemStatusEnum;
use App\Enums\WorkflowStatusEnum;
use App\Models\LineItem;
use App\Models\LineItemWorkflow;
use App\Models\ProductLine;
use App\Models\User;
use App\Models\Workflow;
use App\Models\WorkflowCategory;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class WorkflowService
{
    public function list()
    {
        return WorkflowCategory::with('items')->get();
    }
    public function store(WorkflowDTO $dto)
    {
        $lastSort = (int) Workflow::where('workflow_category_id', $dto->categoryId)->orderByDesc('sort')->value('sort');

        Workflow::create([
            'workflow_category_id' => $dto->categoryId,
            'name' => $dto->name,
            'default_cost' => $dto->cost,
            'sort' => $lastSort + $dto->sort
        ]);
    }
    public function update($id, WorkflowDTO $dto)
    {
        Workflow::where('id', $id)->update([
            'workflow_category_id' => $dto->categoryId,
            'name' => $dto->name,
            'default_cost' => $dto->cost,
            'sort' => $dto->sort,
        ]);
    }
    public function updateItems($request)
    {
        $sort = 1;
        foreach ($request->workflows as $row) {
            Workflow::where('id', $row['id'])->update([
                'sort' => $sort,
                'default_cost' => $row['default_cost']
            ]);

            $sort++;
        }
    }
    public function destroy($id)
    {
        Workflow::where('id', $id)->delete();
    }
    public function waitingList($request)
    {
        // 1. Ambil data line_items dengan status pending beserta relasi terkait
        $query = LineItem::with([
            'currentWorkflow', // Untuk tahu ini sedang di tahapan apa (misal: Menjahit)
            'productLine' // Untuk tahu nama baju / produknya apa
        ])
            ->where('workflow_status', LineItemStatusEnum::PENDING);

        // 2. Fitur Opsional: Filter berdasarkan Kategori Workflow (jika Supervisor ingin memfilter per divisi)
        if ($request->categoryId) {
            $query->whereHas('currentWorkflow', function ($q) use ($request) {
                $q->where('workflow_category_id', $request->categoryId);
            });
        }
        if ($request->workflowId) {
            $query->whereHas('currentWorkflow', function ($q) use ($request) {
                $q->where('workflow_id', $request->workflowId);
            });
        }

        // 3. Urutkan berdasarkan batas waktu terdekat (ETD) agar supervisor tahu mana yang prioritas
        $waitingList = $query->orderBy('etd_finished_at', 'asc')
            ->orderBy('workflow_id', 'asc')
            ->paginate($request->per_page ?? 15); // Menggunakan pagination agar loading cepat

        // dd($waitingList);

        $categories = WorkflowCategory::from('workflow_categories as wc')
            ->join('workflows as w', 'wc.id', 'w.workflow_category_id')
            ->leftJoin('line_items as li', 'li.workflow_id', 'w.id')
            ->select('wc.id', 'wc.name')
            ->selectRaw("COUNT(CASE WHEN li.workflow_status = 'pending' THEN li.id END) as total_count")
            ->groupBy('wc.id', 'wc.name')
            ->get();

        $workflows = [];

        if ($request->categoryId) {
            $workflows = Workflow::where('workflow_category_id', $request->categoryId)
                // ->when($request->workflowId, function ($q) use ($request) {
                //     $q->where('li.workflow_id', $request->workflowId);
                // })
                ->leftJoin('line_items as li', 'li.workflow_id', 'workflows.id')
                ->select('workflows.id', 'workflows.name')
                ->selectRaw("COUNT(CASE WHEN li.workflow_status = 'pending' THEN li.id END) as total_count")
                ->groupBy('workflows.id', 'workflows.name')
                ->orderBy('workflows.sort')
                ->get();;
        }
        return [
            'data' => $waitingList,
            'categories' => $categories,
            'users' => User::all()->map(fn($row) => ['label' => $row->name, 'value' => $row->id]),
            'workflows' => $workflows

        ];
    }
    public function currentProcess($request)
    {
        $query = LineItem::with([
            'currentWorkflow', // Untuk tahu ini sedang di tahapan apa (misal: Menjahit)
            'productLine' => function ($q) {
                $q->select('id', 'sku_number', 'name_override');
            },
            'latestWorkflow',
            'latestWorkflow.worker' => function ($q) {
                $q->select('id', 'name');
            }
        ])
            ->where('workflow_status', 'processing');

        if ($request->categoryId) {
            $query->whereHas('currentWorkflow', function ($q) use ($request) {
                $q->where('workflow_category_id', $request->categoryId);
            });
        }

        if ($request->workflowId) {
            $query->whereHas('currentWorkflow', function ($q) use ($request) {
                $q->where('workflow_id', $request->workflowId);
            });
        }

        $waitingList = $query->orderBy('etd_finished_at', 'asc')
            ->orderBy('workflow_id', 'asc')
            ->paginate($request->per_page ?? 15); // Menggunakan pagination agar loading cepat

        $categories = WorkflowCategory::from('workflow_categories as wc')
            ->join('workflows as w', 'wc.id', 'w.workflow_category_id')
            ->leftJoin('line_items as li', 'li.workflow_id', 'w.id')
            ->select('wc.id', 'wc.name')
            ->selectRaw("COUNT(CASE WHEN li.workflow_status = 'processing' THEN li.id END) as total_count")
            ->groupBy('wc.id', 'wc.name')
            ->get();

        $workflows = [];

        if ($request->categoryId) {
            $workflows = Workflow::where('workflow_category_id', $request->categoryId)
                // ->when($request->workflowId, function ($q) use ($request) {
                //     $q->where('li.workflow_id', $request->workflowId);
                // })
                ->leftJoin('line_items as li', 'li.workflow_id', 'workflows.id')
                ->select('workflows.id', 'workflows.name')
                ->selectRaw("COUNT(CASE WHEN li.workflow_status = 'processing' THEN li.id END) as total_count")
                ->groupBy('workflows.id', 'workflows.name')
                ->orderBy('workflows.sort')
                ->get();;
        }


        return [
            'data' => $waitingList,
            'categories' => $categories,
            'workflows' => $workflows
        ];
    }
    public function readyToShip($request)
    {
        $status = $request->status ?? 'finished';
        $query = LineItem::with([
            'productLine' => function ($q) {
                $q->select('id', 'sku_number', 'name_override');
            },
            'latestWorkflow',
            'latestWorkflow.worker' => function ($q) {
                $q->select('id', 'name');
            }
        ])
            ->where('status', $status);

        return $query->orderBy('etd_finished_at', 'asc')
            ->orderBy('workflow_id', 'asc')
            ->paginate($request->per_page ?? 15); // Menggunakan pagination agar loading cepat

    }
    public function assignWorkerFromRequest($request)
    {
        try {
            return DB::transaction(function () use ($request) {
                foreach ($request->items as $lineItemId) {
                    $this->assignWorker($lineItemId, $request->worker_id);
                }
            });
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    public function assignWorker(string $lineItemId, string $workerId, ?float $customCost = null)
    {
        try {
            return DB::transaction(function () use ($lineItemId, $workerId, $customCost) {
                $lineItem = LineItem::findOrFail($lineItemId);


                // Validasi: Pastikan statusnya memang siap dikerjakan (pending)
                if ($lineItem->workflow_status !== WorkflowStatusEnum::PENDING) {
                    throw new Exception("Item tidak dalam status pending atau sedang dikerjakan oleh orang lain.");
                }

                $currentWorkflow = $lineItem->currentWorkflow;
                if (!$currentWorkflow) {
                    throw new Exception("Workflow aktif tidak ditemukan untuk item ini.");
                }

                $productId = ProductLine::select('product_id')->where('id', $lineItem->product_line_id)->value('product_id');

                $customProductCost = DB::table('product_workflow_costs')
                    ->where('product_id', $productId)
                    ->where('workflow_id', $lineItem->workflow_id)
                    ->value('custom_cost');

                $finalCost = $customCost ?? $customProductCost ?? $currentWorkflow->default_cost;

                // Cek apakah pekerja ini sudah diassign sebelumnya untuk tahapan ini (mencegah double assign)
                $exists = LineItemWorkflow::where('line_item_id', $lineItemId)
                    ->where('workflow_id', $lineItem->workflow_id)
                    ->exists();

                if ($exists) {
                    throw new Exception("Tugas pada tahapan ini sudah dibagikan ke pekerja.");
                }

                // Buat data assignment di line_item_workflows
                $assignment = LineItemWorkflow::create([
                    'line_item_id' => $lineItem->id,
                    'workflow_id'  => $lineItem->workflow_id,
                    'worker_id'    => $workerId,
                    'cost'         => $finalCost,
                    'start_at'     => Carbon::now(),
                ]);

                // Update status Line Item menjadi processing
                $lineItem->update(['workflow_status' => WorkflowStatusEnum::PROCESSING]);

                return $assignment;
            });
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    public function completeCurrentProcess(string $barcode, ?string $description = null)
    {
        return DB::transaction(function () use ($barcode, $description) {
            $lineItem = LineItem::with('currentWorkflow')->where('barcode', $barcode)->firstOrFail();

            if ($lineItem->workflow_status !== WorkflowStatusEnum::PROCESSING) {
                throw new Exception("Hanya item dengan status 'processing' yang bisa diselesaikan.");
            }

            $now = Carbon::now();

            // 1. Update data kerjaan di tabel transaksi (LineItemWorkflow) yang sedang aktif
            $currentAssignment = LineItemWorkflow::where('line_item_id', $lineItem->id)
                ->where('workflow_id', $lineItem->workflow_id)
                ->whereNull('finished_at')
                ->first();

            if ($currentAssignment) {
                $currentAssignment->update([
                    'finished_at' => $now,
                    'description' => $description // typo aman, sesuaikan dengan field migrasi Anda ('desription')
                ]);
            }

            // Akumulasi COGS/HPP berdasarkan biaya tenaga kerja sejauh ini
            $totalCostSoFar = LineItemWorkflow::where('line_item_id', $lineItem->id)->sum('cost');
            $lineItem->production_cost = $totalCostSoFar;

            // 2. Cari proses selanjutnya berdasarkan 'sort' dan kategori workflow yang sama
            $nextWorkflow = Workflow::where('workflow_category_id', $lineItem->currentWorkflow->workflow_category_id)
                ->where('sort', '>', $lineItem->currentWorkflow->sort)
                ->orderBy('sort', 'asc')
                ->first();

            if ($nextWorkflow) {
                // JIKA ADA PROSES BERIKUTNYA: Alihkan workflow_id ke yang baru, status balik ke pending
                $lineItem->workflow_id = $nextWorkflow->id;
                $lineItem->workflow_status = WorkflowStatusEnum::PENDING;
                $lineItem->status = LineItemStatusEnum::PROCESSING;
            } else {
                // JIKA TIDAK ADA LAGI (Sudah Tahap Akhir): Tandai seluruh line item selesai produksi
                // $lineItem->workflow_id = null;
                $lineItem->workflow_status = WorkflowStatusEnum::FINISHED;
                $lineItem->status = LineItemStatusEnum::FINISHED;
                $lineItem->finished_at = $now;
            }

            $lineItem->save();

            return $lineItem;
        });
    }
}
