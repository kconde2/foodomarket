<?php

declare(strict_types=1);

namespace App\Mercuriale\Domain\Model\Product\Enum;

enum ProductImportStatus: string
{
    case SUCCESS = 'success';
    case PARTIAL = 'partial';
    case FAILURE = 'failure';
}
