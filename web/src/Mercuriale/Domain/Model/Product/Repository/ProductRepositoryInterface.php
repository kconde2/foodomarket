<?php

declare(strict_types=1);

namespace App\Mercuriale\Domain\Model\Product\Repository;

use App\Mercuriale\Domain\Model\Product\Entity\Product;
use App\Mercuriale\Domain\Model\Supplier\Entity\Supplier;
use Doctrine\ORM\QueryBuilder;

interface ProductRepositoryInterface
{
    public function find($id);

    public function findAll();

    public function findBy(array $criteria);

    public function findOneBy(array $criteria);

    public function findByCodeAndDescription(string $query): QueryBuilder;

    public function saveOrUpdate(Supplier $supplier, Product $product): Product;
}
