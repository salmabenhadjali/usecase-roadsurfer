<?php

namespace App\Controller;

use App\Service\CollectionService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api')]
class CollectionController extends AbstractController
{
    private CollectionService $collectionService;

    public function __construct(CollectionService $collectionService)
    {
        $this->collectionService = $collectionService;
    }

    #[Route('/list/{type}', methods: ['GET'], requirements: ['type' => 'fruit|vegetable'])]
    public function list(string $type, Request $request): JsonResponse
    {
        $unit = $request->query->get('unit', 'g'); // Default: grams

        $filters = [
            'name' => $request->query->get('name'),
            'minWeight' => $request->query->get('minWeight'),
            'maxWeight' => $request->query->get('maxWeight'),
        ];

        try {
            return $this->json($this->collectionService->list($type, $unit, $filters));
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/add', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $this->collectionService->add($data['type'], $data['name'], $data['quantity'], $data['unit']);
            $this->collectionService->save();
            return $this->json([
                'message' => 'Item added succesfully',
                JsonResponse::HTTP_CREATED
            ]);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}
