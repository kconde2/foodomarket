<?php

declare(strict_types=1);

namespace App\Controller;

use App\Mercuriale\Domain\Model\Product\Repository\ProductRepositoryInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ListMercurialeController extends AbstractController
{
    #[Route('/', name: 'catalog_index')]
    public function __invoke(
        ProductRepositoryInterface $productRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $searchQuery = $request->query->get('q', '');
        $queryBuilder = $productRepository->findByCodeAndDescription($searchQuery);

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('catalog/index.html.twig', [
            'products' => $pagination,
            'searchQuery' => $searchQuery,
        ]);
    }
}
