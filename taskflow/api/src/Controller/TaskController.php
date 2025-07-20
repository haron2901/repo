<?php

namespace App\Controller;

use App\Dto\Task\TaskCreateDto;
use App\Dto\Task\UpdateTaskDto;
use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Repository\TaskQueueRepository;
use App\Service\TaskService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/tasks')]
class TaskController extends AbstractController
{
    public function __construct(
        private TaskService $service,
        private TaskRepository $taskRepository,
        private TaskQueueRepository $queueRepository,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
    ) {}

    #[Route('', name: 'create_task', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $dto = $this->serializer->deserialize($request->getContent(), TaskCreateDto::class, 'json');

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            return $this->json($errors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $queue = $this->queueRepository->find($dto->queueId);
        if (!$queue) {
            return $this->json(['error' => 'Queue not found'], Response::HTTP_NOT_FOUND);
        }

        $task = $this->service->create($dto, $this->getUser(), $queue);

        return $this->json([
            'id' => $task->getId(),
            'title' => $task->getTitle(),
            'description' => $task->getDescription(),
            'status' => $task->getStatus(),
            'createdAt' => $task->getCreatedAt()->format('c'),
            'updatedAt' => $task->getUpdatedAt()->format('c'),
            'queueId' => $queue->getId(),
            'assigneeId' => $task->getAssignee()?->getId(),
            'deadline' => $task->getDeadline()?->format('c'),
        ], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'get_task', methods: ['GET'])]
    public function getTask(int $id): JsonResponse
    {
        $task = $this->taskRepository->find($id);
        if (!$task || $task->getUser()->getId() !== $this->getUser()->getId()) {
            return $this->json(['error' => 'Task not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'id' => $task->getId(),
            'title' => $task->getTitle(),
            'description' => $task->getDescription(),
            'status' => $task->getStatus(),
            'createdAt' => $task->getCreatedAt()->format('c'),
            'updatedAt' => $task->getUpdatedAt()->format('c'),
            'queueId' => $task->getQueue()?->getId(),
            'assigneeId' => $task->getAssignee()?->getId(),
            'deadline' => $task->getDeadline()?->format('c'),
        ]);
    }

    #[Route('/{id}', name: 'update_task', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $task = $this->taskRepository->find($id);
        if (!$task || $task->getUser()->getId() !== $this->getUser()->getId()) {
            return $this->json(['error' => 'Task not found'], Response::HTTP_NOT_FOUND);
        }

        $dto = $this->serializer->deserialize($request->getContent(), UpdateTaskDto::class, 'json');

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            return $this->json($errors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $task = $this->service->update($task, $dto, $this->getUser());

        return $this->json([
            'id' => $task->getId(),
            'title' => $task->getTitle(),
            'description' => $task->getDescription(),
            'status' => $task->getStatus(),
            'createdAt' => $task->getCreatedAt()->format('c'),
            'updatedAt' => $task->getUpdatedAt()->format('c'),
            'queueId' => $task->getQueue()?->getId(),
            'assigneeId' => $task->getAssignee()?->getId(),
            'deadline' => $task->getDeadline()?->format('c'),
        ]);
    }

    #[Route('/{id}', name: 'delete_task', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $task = $this->taskRepository->find($id);
        if (!$task || $task->getUser()->getId() !== $this->getUser()->getId()) {
            return $this->json(['error' => 'Task not found'], Response::HTTP_NOT_FOUND);
        }

        $this->service->delete($task, $this->getUser());

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('', name: 'list_tasks', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $queueId = $request->query->getInt('queueId');

        if ($queueId) {
            $queue = $this->queueRepository->find($queueId);
            if (!$queue || $queue->getOwner()->getId() !== $this->getUser()->getId()) {
                return $this->json(['error' => 'Queue not found'], Response::HTTP_NOT_FOUND);
            }

            $tasks = $this->taskRepository->findBy(['queue' => $queue]);
        } else {
            $tasks = $this->taskRepository->findBy(['user' => $this->getUser()]);
        }

        $data = array_map(fn(Task $task) => [
            'id' => $task->getId(),
            'title' => $task->getTitle(),
            'description' => $task->getDescription(),
            'status' => $task->getStatus(),
            'createdAt' => $task->getCreatedAt()->format('c'),
            'updatedAt' => $task->getUpdatedAt()->format('c'),
            'queueId' => $task->getQueue()?->getId(),
            'assigneeId' => $task->getAssignee()?->getId(),
            'deadline' => $task->getDeadline()?->format('c'),
        ], $tasks);

        return $this->json($data);
    }

}
