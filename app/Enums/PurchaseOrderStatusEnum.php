<?php

namespace App\Enums;

enum PurchaseOrderStatusEnum: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case SHIPPED = 'shipped';
    case REVISION = 'revision';
    case PARTIAL = 'partial';
    case MIXED = 'mixed';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::PROCESSING => 'Diproses',
            self::SHIPPED => 'Dikirim',
            self::REVISION => 'Revisi',
            self::PARTIAL => 'Partial',
            self::MIXED => 'Mixed',
            self::COMPLETED => 'Selesai',
            self::CANCELLED => 'Dibatalkan',
        };
    }
    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'grey-7',
            self::PROCESSING => 'teal',
            self::SHIPPED => 'purple',
            self::PARTIAL => 'purple',
            self::MIXED => 'purple',
            self::REVISION => 'orange',
            self::COMPLETED => 'green',
            self::CANCELLED => 'red',
        };
    }
    public static function options()
    {
        return array_map(function ($row) {
            return [
                'label' => $row->label(),
                'value' => $row->value
            ];
        }, self::cases());
    }
}
