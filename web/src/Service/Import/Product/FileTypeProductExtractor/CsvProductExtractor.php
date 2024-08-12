<?php

declare(strict_types=1);

namespace App\Service\Import\Product\FileTypeProductExtractor;

use App\Mercuriale\Domain\Model\Product\Dto\ProductImportView;
use App\Mercuriale\Domain\Model\Product\Dto\ProductView;
use App\Mercuriale\Domain\Model\Product\Service\ProductSourceExtractorInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class CsvProductExtractor implements ProductSourceExtractorInterface
{
    public const COLUMNS_COUNT = 3;

    public function __construct(
        private SerializerInterface $serializer,
        private LoggerInterface $logger,
        private ValidatorInterface $validator
    ) {
    }

    public function supports(string $sourceType): bool
    {
        return 'csv' === $sourceType;
    }

    /**
     * Extract data from CSV file.
     */
    public function getData(string $filePath): ProductImportView
    {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            throw new \InvalidArgumentException("File does not exist or is not readable: $filePath");
        }

        $handle = fopen($filePath, 'r');

        $products = [];
        $errors = [];
        $lineNumber = 0;

        while (false !== ($data = fgetcsv($handle, 0, ','))) {
            ++$lineNumber;

            if (self::COLUMNS_COUNT !== \count($data)) {
                $errors[] = \sprintf('Line %d: Invalid number of columns. Expected 3, got %d.', $lineNumber, \count($data));
                continue;
            }

            try {
                $product = new ProductView(...$data);

                $validationErrors = $this->validator->validate($product);

                if (\count($validationErrors) > 0) {
                    $errorMessages = array_map(fn ($error) => $error->getMessage(), iterator_to_array($validationErrors));

                    $errors[] = \sprintf('Line %d: %s (Code : %s)', $lineNumber, implode(', ', $errorMessages), $data[1]);
                    continue;
                }

                $products[] = $product;
            } catch (\Exception $e) {
                $errors[] = \sprintf('Line %d: %s', $lineNumber, $e->getMessage());
            }
        }

        fclose($handle);

        return new ProductImportView($products, $errors);
    }
}
