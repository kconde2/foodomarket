<?php

declare(strict_types=1);

namespace App\Message;

final class ImportProductData
{
    public function __construct(
        public readonly string $supplierName,
        public readonly string $fileName
    ) {
    }
}
