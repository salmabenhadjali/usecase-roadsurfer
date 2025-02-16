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
        if (!in_array($type, ['fruit', 'vegetable'])) {
            throw new \InvalidArgumentException("The type must be either 'fruit' or 'vegetable'.");
        }

        if (!in_array($unit, ['g', 'kg'])) {
            throw new \InvalidArgumentException("The unit must be either 'g' or 'kg'.");
        }

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

    public function list(string $type, ?string $unit = 'g', ?array $filters = []): array
    {
        if (!in_array($type, ['fruit', 'vegetable'])) {
            throw new \InvalidArgumentException("The type must be either 'fruit' or 'vegetable'.");
        }

        if (!in_array($unit, ['g', 'kg'])) {
            throw new \InvalidArgumentException("The unit must be either 'g' or 'kg'.");
        }

        $repository = ($type === 'fruit') ? Fruit::class : Vegetable::class;
        $queryBuilder = $this->entityManager->getRepository($repository)->createQueryBuilder('i');
        // Apply filters
        if (!empty($filters['name'])) {
            $queryBuilder->andWhere('i.name LIKE :name')
                ->setParameter('name', '%' . $filters['name'] . '%');
        }

        if (!empty($filters['minWeight'])) {
            $queryBuilder->andWhere('i.weight >= :minWeight')
                ->setParameter('minWeight', $filters['minWeight']);
        }

        if (!empty($filters['maxWeight'])) {
            $queryBuilder->andWhere('i.weight <= :maxWeight')
                ->setParameter('maxWeight', $filters['maxWeight']);
        }

        $items = $queryBuilder->getQuery()->getResult();

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
