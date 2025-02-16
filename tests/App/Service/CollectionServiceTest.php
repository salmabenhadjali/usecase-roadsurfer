<?php

namespace App\Tests\App\Service;

use App\Service\CollectionService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CollectionServiceTest extends KernelTestCase
{
    private CollectionService $collectionService;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $this->collectionService = $container->get(CollectionService::class);
    }

    public function testProcessJsonFile(): void
    {
        // Reset data if not empty
        $this->collectionService->removeAll();

        //Retrieve data
        $fruits = $this->collectionService->list('fruit');
        $vegetables = $this->collectionService->list('vegetable');

        // Assertions
        $this->assertEmpty($fruits);
        $this->assertEmpty($vegetables);

        $filePath = self::$kernel->getProjectDir() . '/request.json';
        $this->assertFileExists($filePath);

        // Process file
        $this->collectionService->processJsonFile($filePath);

        //Retrieve data
        $fruits = $this->collectionService->list('fruit');
        $vegetables = $this->collectionService->list('vegetable');

        // Assertions
        $this->assertNotEmpty($fruits);
        $this->assertNotEmpty($vegetables);
    }

    public function testAdd(): void
    {
        $this->collectionService->add('fruit', 'Mango', 3, 'kg');
        $this->collectionService->save();

        $fruits = $this->collectionService->list('fruit');
        $fruit = $fruits[count($fruits) - 1];

        // Assertions
        $this->assertNotEmpty($fruits);
        $this->assertEquals('Mango', $fruit['name']);
        $this->assertEquals(3000, $fruit['weight']);
    }

    public function testRemove(): void
    {
        // Reset data if not empty
        $this->collectionService->removeAll();

        // Add one element
        $this->collectionService->add('vegetable', 'Potato', 500);
        $this->collectionService->save();

        // Retrieve list of fruits to get the ID
        $vegetables = $this->collectionService->list('vegetable');
        $vegetable = $vegetables[0];

        // Remove it
        $this->collectionService->remove('vegetable', $vegetable['id']);

        // Retrieve list after deletion
        $vegetablesAfter = $this->collectionService->list('vegetable');

        // Assertions
        $this->assertEmpty($vegetablesAfter);
    }
}
