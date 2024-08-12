<?php

declare(strict_types=1);

namespace App\Service\Import\Product;

use App\Mercuriale\Domain\Model\Product\Service\ProductExtractorInterface;

final class ProductExtractor implements ProductExtractorInterface
{
    /**
     * @param iterable<ProductFileTypeExtractorInterface> $importers
     */
    public function __construct(
        private readonly ProductFileTypeDetector $fileTypeDetector,
        private readonly iterable $importers
    ) {
    }

    public function getData(string $filePath): array
    {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            throw new \InvalidArgumentException("The file $filePath does not exist or is not readable.");
        }

        $fileType = $this->fileTypeDetector->detect($filePath);

        foreach ($this->importers as $importer) {
            if ($importer->supports($fileType)) {
                return $importer->getData($filePath);
            }
        }

        throw new \InvalidArgumentException("No suitable importer found for file type: $fileType");
    }
}
