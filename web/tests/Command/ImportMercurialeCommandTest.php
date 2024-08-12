<?php

declare(strict_types=1);

namespace App\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class ImportMercurialeCommandTest extends KernelTestCase
{
    private Application $application;
    private CommandTester $commandTester;

    protected function setUp(): void
    {
        // Arrange common setup
        self::bootKernel();
        $this->application = new Application(self::$kernel);
        $command = $this->application->find('app:import-mercuriale');
        $this->commandTester = new CommandTester($command);
    }

    public function test_execute_successful_import(): void
    {
        // Act
        $this->commandTester->execute([
            'name' => 'dairyland',
            'file' => 'dairyland-semaine-1.csv',
        ]);

        // Assert
        $this->commandTester->assertCommandIsSuccessful();
        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('File imported successfully.', $output);
    }

    public function test_execute_file_not_found(): void
    {
        // Arrange
        // Get the container and the parameter
        $container = self::$kernel->getContainer();
        $importFilesDir = $container->getParameter('product_upload_command_directory');
        $fileName = 'non_existing_file.csv';
        $expectedFilePath = $importFilesDir.'/'.$fileName;

        // Act
        $this->commandTester->execute([
            'name' => 'Govegi',
            'file' => $fileName,
        ]);

        // Assert
        $this->assertSame(Command::FAILURE, $this->commandTester->getStatusCode());
        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString("The file at path $expectedFilePath does not exist.", $output);
    }

    public function test_execute_invalid_supplier_name(): void
    {
        // Act
        $this->commandTester->execute([
            'name' => '',
            'file' => 'example.csv',
        ]);

        // Assert
        $this->assertSame(Command::FAILURE, $this->commandTester->getStatusCode());
        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('The supplier name is required and cannot be empty.', $output);
    }

    public function test_execute_supplier_not_found(): void
    {
        // Act
        $this->commandTester->execute([
            'name' => 'NonExistentSupplier',
            'file' => 'dairyland-semaine-1.csv',
        ]);

        // Assert
        $this->assertSame(Command::FAILURE, $this->commandTester->getStatusCode());
        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('Supplier not found:', $output);
    }
}
