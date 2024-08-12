<?php

declare(strict_types=1);

namespace App\Mercuriale\UseCase\Product\ImportProductList;

use App\Event\ProductImportedEvent;
use App\Mercuriale\Domain\Model\Product\Dto\ProductView;
use App\Mercuriale\Domain\Model\Product\Entity\Product;
use App\Mercuriale\Domain\Model\Product\Enum\ProductImportStatus;
use App\Mercuriale\Domain\Model\Product\Repository\ProductRepositoryInterface;
use App\Mercuriale\Domain\Model\Product\Service\ImageFetcherInterface;
use App\Mercuriale\Domain\Model\Product\Service\ProductSourceExtractorInterface;
use App\Mercuriale\Domain\Model\Product\ValueObject\ProductPrice;
use App\Mercuriale\Domain\Model\Supplier\Entity\Supplier;
use App\Mercuriale\Domain\Model\Supplier\Exception\SupplierNotFoundException;
use App\Mercuriale\Domain\Model\Supplier\Repository\SupplierRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class ImportProductListHandler
{
    public function __construct(
        private LoggerInterface $logger,
        private ProductRepositoryInterface $productRepository,
        private SupplierRepositoryInterface $supplierRepository,
        private ProductSourceExtractorInterface $productSourceExtractor,
        private EventDispatcherInterface $eventDispatcher,
        private ImageFetcherInterface $imageFetcher
    ) {
    }

    public function __invoke(ImportProductListInput $importProductListInput): void
    {
        // verify that the supplier exists
        $supplierName = $importProductListInput->supplierName;
        $supplier = $this->supplierRepository->findOneBy(['name' => $supplierName]);

        if (!$supplier) {
            throw new SupplierNotFoundException($supplierName);
        }

        // get product data
        $productImportView = $this->productSourceExtractor->getData($importProductListInput->fileName);
        $products = $this->convertToProducts($supplier, $productImportView->products);

        // import
        $this->logger->info("Starting product import for supplier: {$supplierName}");

        foreach ($products as $productData) {
            $this->processProductData($supplier, $productData);
        }

        $this->logger->info("Finished product import for supplier: {$supplierName}");

        $importStatus = \count($productImportView->errors) > 0 ? ProductImportStatus::PARTIAL : ProductImportStatus::SUCCESS;

        $this->eventDispatcher->dispatch(new ProductImportedEvent(
            $productImportView,
            $importStatus,
            $supplierName
        ));
    }

    private function processProductData(Supplier $supplier, Product $productData): void
    {
        $this->productRepository->saveOrUpdate($supplier, $productData);

        $this->logger->info('Processed product: '.$productData->getCode());
    }

    /**
     * Converts raw product data into Product entities.
     *
     * @param array<ProductView> $productDataArray
     *
     * @return array<Product>
     */
    private function convertToProducts(Supplier $supplier, array $productDataArray): array
    {
        $products = [];

        foreach ($productDataArray as $productData) {
            $products[] = $this->createProduct($supplier, $productData);
        }

        return $products;
    }

    private function createProduct(Supplier $supplier, ProductView $productData): Product
    {
        $imageUrl = $this->imageFetcher->fetchImageUrl($productData->description);

        return new Product(
            code: $productData->code,
            description: $productData->description,
            price: new ProductPrice($productData->price),
            supplier: $supplier,
            imageUrl: $imageUrl
        );
    }
}
