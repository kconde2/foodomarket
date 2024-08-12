<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Mercuriale\Domain\Model\Product\Entity\Product;
use App\Mercuriale\Domain\Model\Product\ValueObject\ProductPrice;
use App\Mercuriale\Domain\Model\Supplier\Entity\Supplier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ListMercurialeControllerTest extends WebTestCase
{
    public function test_index_page_is_accessible(): void
    {
        // Arrange
        $client = static::createClient();

        // Act
        $client->request('GET', '/');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('table');
        $this->assertSelectorExists('form[action=""]');
    }

    public function test_search_functionality_with_results(): void
    {
        // Arrange
        $client = static::createClient();
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);

        $govegiSupplier = $entityManager->getRepository(Supplier::class)->findOneBy(['name' => 'Govegi']);
        $productDescription = 'SampleProduct';
        $product = new Product('CODE-X', $productDescription, new ProductPrice('2.47'), $govegiSupplier);
        $entityManager->persist($product);
        $entityManager->flush();

        // Act
        $client->request('GET', '/', ['q' => $productDescription]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('table tbody tr');
        $this->assertSelectorTextContains('table tbody tr td.supplier-name', 'Govegi');
        $this->assertSelectorTextContains('table tbody tr td.description', 'SampleProduct');
    }

    public function test_search_functionality_with_no_results(): void
    {
        // Arrange
        $client = static::createClient();
        $searchQuery = 'NonExistentProduct';

        // Act
        $client->request('GET', '/', [
            'q' => $searchQuery,
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('table');
        $this->assertSelectorExists('td.text-gray-500');
        $this->assertSelectorNotExists('td.py-2.px-4');
        $this->assertSelectorTextContains('td.text-gray-500', 'Aucun produit trouvé.');
    }

    public function test_pagination_is_working(): void
    {
        // Arrange
        $client = static::createClient();
        $page = 2;

        // Act
        $client->request('GET', '/', [
            'page' => $page,
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.pagination');
        $this->assertSelectorTextContains('.pagination', (string) $page);
    }
}
