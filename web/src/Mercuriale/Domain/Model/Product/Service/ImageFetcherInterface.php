<?php

declare(strict_types=1);

namespace App\Mercuriale\Domain\Model\Product\Service;

interface ImageFetcherInterface
{
    public function fetchImageUrl(string $query): ?string;
}
