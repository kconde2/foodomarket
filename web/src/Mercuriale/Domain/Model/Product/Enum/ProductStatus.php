<?php

declare(strict_types=1);

namespace App\Mercuriale\Domain\Model\Product\Enum;

enum ProductStatus: string
{
    case Imported = 'imported';
    case Validated = 'validated';
}
