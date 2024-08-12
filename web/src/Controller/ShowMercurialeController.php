<?php

declare(strict_types=1);

namespace App\Controller;

use App\Mercuriale\Domain\Model\Product\Entity\Product;
use App\Mercuriale\UseCase\Product\ValidateProduct\ValidateProductHandler;
use App\Mercuriale\UseCase\Product\ValidateProduct\ValidateProductInput;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Workflow\WorkflowInterface;

final class ShowMercurialeController extends AbstractController
{
    public function __construct(
        #[Target('product_validation')]
        private WorkflowInterface $productValidationWorkflow,
        private readonly ValidateProductHandler $validateProductHandler,
    ) {
    }

    #[Route('/products/{id}', name: 'product_show')]
    public function __invoke(Product $product, Request $request): Response
    {
        $canValidate = $this->productValidationWorkflow->can($product, 'validate');
        if ($request->isMethod('POST') && $canValidate) {
            $validateProductHandler = $this->validateProductHandler;
            $validateProductHandler(new ValidateProductInput($product));

            $this->addFlash('success', "Le produit {$product->getCode()} a été validé avec succès.");

            return $this->redirectToRoute('catalog_index');
        }

        return $this->render('catalog/show.html.twig', [
            'product' => $product,
            'canValidate' => $canValidate,
        ]);
    }
}
