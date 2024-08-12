<?php

declare(strict_types=1);

namespace App\Mercuriale\UseCase\Product\ValidateProduct;

use App\Mercuriale\Domain\Model\Product\Enum\ProductStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\Workflow\WorkflowInterface;

final class ValidateProductHandler
{
    public function __construct(
        #[Target('product_validation')]
        private WorkflowInterface $productValidationWorkflow,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function __invoke(ValidateProductInput $validateProductInput): void
    {
        $product = $validateProductInput->product;

        $this->productValidationWorkflow->apply($product, 'validate');

        $product->setStatus(ProductStatus::Validated);

        $this->entityManager->flush();
    }
}
