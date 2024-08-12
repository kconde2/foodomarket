<?php

declare(strict_types=1);

namespace App\Command;

use App\Mercuriale\Domain\Model\Product\Service\ProductExtractorInterface;
use App\Mercuriale\Domain\Model\Supplier\Exception\SupplierNotFoundException;
use App\Mercuriale\UseCase\Product\ImportProductList\ImportProductListHandler;
use App\Mercuriale\UseCase\Product\ImportProductList\ImportProductListInput;
use App\Service\Import\Product\ProductFileTypeDetector;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[AsCommand(
    name: 'app:import-mercuriale',
    description: 'Imports a Mercuriale (price list) file into the system'
)]
final class ImportMercurialeCommand extends Command
{
    public function __construct(
        private readonly ProductFileTypeDetector $fileTypeDetector,
        private readonly ParameterBagInterface $params,
        private readonly ProductExtractorInterface $productExtractor,
        private readonly ImportProductListHandler $importProductListHandler,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'The supplier name')
            ->addArgument('file', InputArgument::REQUIRED, 'The name of the file to import (located in the product directory)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $supplierName = $input->getArgument('name');
        $fileName = $input->getArgument('file');

        if (!$this->validateSupplierName($supplierName, $io)) {
            return Command::FAILURE;
        }

        $filePath = $this->getFilePath($fileName);

        if (!$this->validateFilePath($filePath, $io)) {
            return Command::FAILURE;
        }

        return $this->processImport($supplierName, $filePath, $io);
    }

    private function validateSupplierName(?string $supplierName, SymfonyStyle $io): bool
    {
        if (empty($supplierName)) {
            $io->error('The supplier name is required and cannot be empty.');

            return false;
        }

        return true;
    }

    private function getFilePath(string $fileName): string
    {
        $importFilesDir = $this->params->get('product_upload_command_directory');

        return $importFilesDir.'/'.$fileName;
    }

    private function validateFilePath(string $filePath, SymfonyStyle $io): bool
    {
        if (!file_exists($filePath)) {
            $io->error("The file at path $filePath does not exist.");

            return false;
        }

        return true;
    }

    private function processImport(string $supplierName, string $fileName, SymfonyStyle $io): int
    {
        try {
            $io->text("Starting import process for supplier: $supplierName");

            $importProductListInput = new ImportProductListInput($supplierName, $fileName);

            ($this->importProductListHandler)($importProductListInput);

            $io->success('File imported successfully.');

            return Command::SUCCESS;
        } catch (SupplierNotFoundException $e) {
            $io->error('Supplier not found: '.$e->getMessage());

            return Command::FAILURE;
        } catch (\InvalidArgumentException $e) {
            $io->error('Invalid argument: '.$e->getMessage());

            return Command::FAILURE;
        } catch (\Exception $e) {
            $io->error('An error occurred during import: '.$e->getMessage());

            return Command::FAILURE;
        }
    }
}
