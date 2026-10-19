<?php

namespace App\Enums;

enum WorkflowStatusEnum: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case FINISHED = 'finished';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::PROCESSING => 'Diproses',
            self::FINISHED => 'Finish',
        };
    }
    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'grey-7',
            self::PROCESSING => 'teal',
            self::FINISHED => 'green',
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