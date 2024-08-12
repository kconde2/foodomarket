<?php

declare(strict_types=1);

namespace App\Mercuriale\UseCase\Product\ValidateProduct;

use App\Mercuriale\Domain\Model\Product\Entity\Product;

final class ValidateProductInput
{
    public function __construct(
        public readonly Product $product
    ) {
    }
}
