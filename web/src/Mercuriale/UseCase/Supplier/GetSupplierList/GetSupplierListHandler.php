<?php

declare(strict_types=1);

namespace App\Mercuriale\UseCase\Supplier\GetSupplierList;

use App\Mercuriale\Domain\Model\Supplier\Repository\SupplierRepositoryInterface;

final class GetSupplierListHandler
{
    public function __construct(private SupplierRepositoryInterface $supplierRepository)
    {
    }

    public function __invoke(?GetSuppliersInput $getSuppliersInput = null): array
    {
        return $this->supplierRepository->findAll();
    }
}
