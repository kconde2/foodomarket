<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImportMercurialeControllerTest extends WebTestCase
{
    public function test_import_page_is_accessible(): void
    {
        // Arrange
        $client = static::createClient();

        // Act
        $crawler = $client->request('GET', '/catalog/import');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form[action="/catalog/import"]');
    }

    public function test_import_file_with_invalid_file(): void
    {
        // Arrange
        $client = static::createClient();

        // Act
        $client->request('POST', '/catalog/import', [
            'supplier_name' => 'Dairyland',
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.alert-danger');
        $this->assertSelectorTextContains('.alert-danger', 'The file is invalid or missing.');
    }

    public function test_import_file_with_invalid_supplier(): void
    {
        // Arrange
        $client = static::createClient();
        $originalFilePath = __DIR__.'/../resources/command/product/dairyland-semaine-1.csv';
        $temporaryFilePath = sys_get_temp_dir().'/dairyland-semaine-1.csv';
        copy($originalFilePath, $temporaryFilePath);

        // Create an UploadedFile instance pointing to the temporary file
        $uploadedFile = new UploadedFile(
            $temporaryFilePath,
            'dairyland-semaine-1.csv',
            'text/csv',
            null,
            true
        );

        // Act
        $client->request('POST', '/catalog/import', [
            'supplier_name' => '',
        ], [
            'file' => $uploadedFile,
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.alert-danger');
    }

    public function test_execute_file_not_found(): void
    {
        // Arrange
        $client = static::createClient();
        $supplierName = 'Govegi';

        // Act
        $client->request('POST', '/catalog/import', [
            'supplier_name' => $supplierName,
        ], [
            // No file is provided here to simulate the missing file scenario
        ]);

        // Assert
        $this->assertResponseStatusCodeSame(200);
        $this->assertSelectorExists('.alert-danger');
        $this->assertSelectorTextContains('.alert-danger', 'The file is invalid or missing.');
    }

    public function test_import_file_with_valid_data(): void
    {
        // Arrange
        $client = static::createClient();
        $originalFilePath = __DIR__.'/../resources/command/product/dairyland-semaine-1.csv';
        $temporaryFilePath = sys_get_temp_dir().'/dairyland-semaine-1.csv';
        copy($originalFilePath, $temporaryFilePath);

        $uploadedFile = new UploadedFile(
            $temporaryFilePath,
            'dairyland-semaine-1.csv',
            'text/csv',
            null,
            true
        );

        // Act
        $client->request('POST', '/catalog/import', [
            'supplier_name' => 'Govegi',
        ], [
            'file' => $uploadedFile,
        ]);

        // Assert
        $client->followRedirect();

        $this->assertSelectorExists('.bg-green-100.text-green-800');
        $this->assertSelectorTextContains('.bg-green-100.text-green-800 p', "Le processus d'importation a commencé. Vous recevrez un email une fois l'importation terminée.");

        // Clean up the temporary file
        @unlink($temporaryFilePath);
    }
}
