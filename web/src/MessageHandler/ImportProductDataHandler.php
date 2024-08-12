<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Mercuriale\UseCase\Product\ImportProductList\ImportProductListHandler;
use App\Mercuriale\UseCase\Product\ImportProductList\ImportProductListInput;
use App\Message\ImportProductData;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class ImportProductDataHandler
{
    public function __construct(
        private LoggerInterface $logger,
        private ImportProductListHandler $importProductListHandler,
    ) {
    }

    public function __invoke(ImportProductData $message): void
    {
        try {
            $importProductListInput = new ImportProductListInput(
                $message->supplierName,
                $message->fileName
            );

            ($this->importProductListHandler)($importProductListInput);
        } catch (\Exception $e) {
            $this->logger->error("Error during import for supplier: {$message->supplierName}, Error: ".$e->getMessage());
        }
    }
}
