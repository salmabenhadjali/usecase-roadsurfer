<?php

namespace App\Tests\App\Service;

use App\Service\CollectionService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CollectionServiceTest extends KernelTestCase
{
    private CollectionService $collectionService;

    public static function setUpBeforeClass(): void
    {
        fwrite(STDOUT, "\nStarting CollectionService Tests...\n");
    }

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $this->collectionService = $container->get(CollectionService::class);

        fwrite(STDOUT, "\nRunning Test: " . $this->getName() . "\n");
    }

    /**
     * @testdox A user can successfully add a fruit with a valid name and weight
     */
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

    /**
     * @testdox A user can successfully remove a vegetable
     */
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

    /**
     * @testdox Processing a JSON file correctly separates fruits and vegetables into their respective collections
     */
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

    /**
     * @testdox An invalid type should return an error when trying to add an item
     */
    public function testInvalidTypeThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("The type must be either 'fruit' or 'vegetable'.");

        // Attempt to add an invalid type
        $this->collectionService->add('meat', 'Steak', 500, 'g');
    }

    /**
     * @testdox An invalid unit should return an error when trying to add an item
     */
    public function testInvalidUnitThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("The unit must be either 'g' or 'kg'.");

        // Attempt to add an invalid type
        $this->collectionService->add('fruit', 'Steak', 500, 'litters');
    }
}
