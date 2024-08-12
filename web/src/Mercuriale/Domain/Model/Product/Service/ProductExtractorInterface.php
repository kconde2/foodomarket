<?php

declare(strict_types=1);

namespace App\Mercuriale\Domain\Model\Product\Service;

interface ProductExtractorInterface
{
    public function getData(string $filePath): array;
}
