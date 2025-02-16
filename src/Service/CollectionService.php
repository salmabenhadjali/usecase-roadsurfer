<?php

namespace App\Service;

use App\Entity\Fruit;
use App\Entity\Vegetable;
use Doctrine\ORM\EntityManagerInterface;

class CollectionService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function add(string $type, string $name, int $weight, string $unit = 'g'): void
    {
        $item = ($type === 'fruit') ? new Fruit() : new Vegetable();
        $item->setName($name);
        $item->setWeight($weight, $unit);

        $this->entityManager->persist($item);
    }

    public function save(): void
    {
        $this->entityManager->flush();
    }

    public function processJsonFile(string $filePath): void
    {
        if (!file_exists($filePath)) {
            throw new \Exception('File not found: ' . $filePath);
        }

        $items = json_decode(file_get_contents($filePath), true);

        if (!is_array($items)) {
            throw new \Exception('Invalid JSON format');
        }

        foreach ($items as $item) {
            $this->add($item['type'], $item['name'], $item['quantity'], $item['unit']);
        }
        $this->save();
    }

    public function list(string $type, ?string $unit = 'g'): array
    {
        $repository = ($type === 'fruit') ? Fruit::class : Vegetable::class;
        $items = $this->entityManager->getRepository($repository)->findAll();

        return array_map(fn($item) => [
            'id' => $item->getId(),
            'name' => $item->getName(),
            'weight' => $item->getWeight($unit),
            'unit' => $unit,
        ], $items);
    }

    public function remove(string $type, int $id): void
    {
        $repository = ($type === 'fruit') ? Fruit::class : Vegetable::class;
        $item = $this->entityManager->getRepository($repository)->find($id);

        if ($item) {
            $this->entityManager->remove($item);
            $this->entityManager->flush();
        }
    }

    public function removeAll(): void
    {
        $this->entityManager->createQuery('DELETE FROM App\Entity\Fruit')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\Vegetable')->execute();
    }
}
