<?php

declare(strict_types=1);

namespace App\Mercuriale\Domain\Model\Product\Dto;

final class ProductImportView
{
    public function __construct(
        /** @var array<ProductView> */
        public readonly array $products,

        /** @var array<string> */
        public readonly array $errors,
    ) {
    }
}
