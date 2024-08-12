<?php

declare(strict_types=1);

namespace App\Mercuriale\UseCase\Product\ImportProductList;

final class ImportProductListInput
{
    public function __construct(
        public readonly string $supplierName,
        public readonly string $fileName,
    ) {
    }
}
