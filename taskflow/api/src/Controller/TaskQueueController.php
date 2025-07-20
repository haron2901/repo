<?php

namespace App\Controller;

use App\Dto\TaskQueue\CreateTaskQueueDto;
use App\Entity\TaskQueue;
use App\Repository\TaskQueueRepository;
use App\Service\TaskQueueService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/queues')]
class TaskQueueController extends AbstractController
{
    public function __construct(
        private TaskQueueService $service,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
        private TaskQueueRepository $repo
    ) {}

    #[Route('', name: 'create_queue', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $dto = $this->serializer->deserialize($request->getContent(), CreateTaskQueueDto::class, 'json');

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            return $this->json($errors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $queue = $this->service->create($dto, $this->getUser());

        return $this->json([
            'id' => $queue->getId(),
            'name' => $queue->getName()
        ], Response::HTTP_CREATED);
    }

    #[Route('', name: 'list_queues', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $queues = $this->repo->findBy(['owner' => $this->getUser()]);

        $data = array_map(fn(TaskQueue $q) => [
            'id' => $q->getId(),
            'name' => $q->getName()
        ], $queues);

        return $this->json($data);
    }
}
