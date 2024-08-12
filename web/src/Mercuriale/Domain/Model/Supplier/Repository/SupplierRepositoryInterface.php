<?php

declare(strict_types=1);

namespace App\Mercuriale\Domain\Model\Supplier\Repository;

use App\Mercuriale\Domain\Model\Supplier\Entity\Supplier;

interface SupplierRepositoryInterface
{
    public function find($id);

    public function findAll();

    public function findBy(array $criteria): array;

    /**
     * @return ?Supplier|null
     */
    public function findOneBy(array $criteria, ?array $orderBy = null): ?object;
}
