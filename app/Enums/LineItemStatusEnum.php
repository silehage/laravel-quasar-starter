<?php

namespace App\Enums;

enum LineItemStatusEnum: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case SHIPPED = 'shipped';
    case FINISHED = 'finished';
    case REVISION = 'revision';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::PROCESSING => 'Diproses',
            self::SHIPPED => 'Dikirim',
            self::REVISION => 'Revisi',
            self::COMPLETED => 'Selesai',
            self::FINISHED => 'Finish',
            self::CANCELLED => 'Dibatalkan',
        };
    }
    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'grey-7',
            self::PROCESSING => 'teal',
            self::SHIPPED => 'purple',
            self::REVISION => 'orange',
            self::FINISHED => 'green',
            self::COMPLETED => 'green',
            self::CANCELLED => 'red',
            default => 'grey-7'
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
    public static function productionOptions()
    {
        return array_map(function ($row) {
            return [
                'label' => $row->label(),
                'value' => $row->value
            ];
        }, [
            self::FINISHED,
            self::SHIPPED, 
            self::COMPLETED, 
        ]);
    }
}
