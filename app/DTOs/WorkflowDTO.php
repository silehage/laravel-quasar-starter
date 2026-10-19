<?php

namespace App\DTOs;

final class WorkflowDTO
{
    public function __construct(
        readonly string $categoryId,
        readonly string $name,
        readonly int $cost,
        readonly int $sort,
    ) {}
}
