<?php

declare(strict_types=1);

namespace App\Repository;

use App\Mercuriale\Domain\Model\Product\Entity\Product;
use App\Mercuriale\Domain\Model\Product\Repository\ProductRepositoryInterface;
use App\Mercuriale\Domain\Model\Supplier\Entity\Supplier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
final class ProductRepository extends ServiceEntityRepository implements ProductRepositoryInterface
{
    public function __construct(ManagerRegistry $registry, private EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Product::class);
    }

    public function saveOrUpdate(Supplier $supplier, Product $newProduct): Product
    {
        /** @var Product|null */
        $existingProduct = $this->findOneBy([
            'code' => $newProduct->getCode(),
            'supplier' => $supplier,
        ]);

        if ($existingProduct) {
            $existingProduct->update(
                description: $newProduct->getDescription(),
                price: $newProduct->price(),
                imageUrl: $newProduct->getImageUrl(),
            );
            $newProduct = $existingProduct;
        } else {
            $newProduct->setSupplier($supplier);
            $this->getEntityManager()->persist($newProduct);
        }

        $this->getEntityManager()->flush();

        return $newProduct;
    }

    public function findByCodeAndDescription(string $query): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->where('p.description LIKE :query OR p.code LIKE :query')
            ->setParameter('query', '%'.$query.'%')
            ->orderBy('p.createdAt', 'DESC');
    }
}
