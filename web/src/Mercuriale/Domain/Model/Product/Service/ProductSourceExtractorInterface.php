<?php

declare(strict_types=1);

namespace App\Mercuriale\Domain\Model\Product\Service;

use App\Mercuriale\Domain\Model\Product\Dto\ProductImportView;

interface ProductSourceExtractorInterface
{
    public function supports(string $sourceType): bool;

    public function getData(string $filePath): ProductImportView;
}
