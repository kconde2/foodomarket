<?php

declare(strict_types=1);

namespace App\Event;

use App\Mercuriale\Domain\Model\Product\Dto\ProductImportView;
use App\Mercuriale\Domain\Model\Product\Enum\ProductImportStatus;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * This event is dispatched each time product import is done.
 */
final class ProductImportedEvent extends Event
{
    public function __construct(
        public readonly ProductImportView $productImportView,
        public readonly ProductImportStatus $importStatus,
        public readonly string $supplierName,
    ) {
    }
}
