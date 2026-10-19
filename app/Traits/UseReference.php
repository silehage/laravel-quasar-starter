<?php

namespace App\Traits;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;

trait UseReference
{
   public static function bootUseReference()
   {

      static::creating(function ($model) {

         if (!$model->reference) {

            $prefix = match (get_class($model)) {
               PurchaseOrder::class => 'PO',
               PurchaseOrderItem::class => 'PI',
               default    => 'TRX'
            };

            $year = date('Ym', strtotime($model->date ?? now()->toDateString()));

            $basePrefix = "{$prefix}{$year}";

            // Ambil no terakhir berdasarkan prefix dan tahun
            $last = static::where('reference', 'like', $basePrefix . '%')
               ->orderByDesc('id')
               ->select('reference')
               ->value('reference');

            $length = 5;

            if ($last) {
               // Extract nomor terakhir
               $lastNumber = intval(substr($last, -$length));
               $next = str_pad($lastNumber + 1, $length, '0', STR_PAD_LEFT);
            } else {
               $next = str_pad(1, $length, '0', STR_PAD_LEFT);
            }

            // Log::debug($model);
            $model->reference =  $basePrefix . $next;
         }
      });
   }
}
