<?php
// src/Service/TaskService.php

namespace App\Service;

use App\Dto\Task\TaskCreateDto;
use App\Dto\Task\UpdateTaskDto;
use App\Entity\Task;
use App\Entity\User;
use App\Entity\TaskQueue;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;

class TaskService
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserRepository $userRepository,
    ) {}

    public function create(TaskCreateDto $dto, User $owner, TaskQueue $queue): Task
    {
        $task = new Task();
        $task->setTitle($dto->title);
        $task->setDescription($dto->description);
        $task->setStatus('new');
        $task->setUser($owner);
        $task->setQueue($queue);

        // Исполнитель
        if ($dto->assigneeId !== null) {
            $assignee = $this->userRepository->find($dto->assigneeId);
            if ($assignee) {
                $task->setAssignee($assignee);
            }
        }

        if ($dto->deadline !== null) {
            $deadline = \DateTimeImmutable::createFromFormat('Y-m-d', $dto->deadline);
            if ($deadline) {
                $task->setDeadline($deadline);
            }
        }

        $this->em->persist($task);
        $this->em->flush();

        return $task;
    }

    public function update(Task $task, UpdateTaskDto $dto, User $owner): Task
    {
        if ($dto->title !== null) {
            $task->setTitle($dto->title);
        }

        if ($dto->description !== null) {
            $task->setDescription($dto->description);
        }

        if ($dto->status !== null) {
            $task->setStatus($dto->status);
        }

        if ($dto->assigneeId !== null) {
            $assignee = $this->userRepository->find($dto->assigneeId);
            $task->setAssignee($assignee);
        }
        if ($dto->deadline !== null) {
            $deadline = \DateTimeImmutable::createFromFormat('Y-m-d', $dto->deadline);
            if ($deadline) {
                $task->setDeadline($deadline);
            }
        }

        // Обновляем updatedAt
        $task->setUpdatedAt(new \DateTimeImmutable());

        $this->em->flush();

        return $task;
    }

    public function delete(Task $task, User $owner): void
    {
        $this->em->remove($task);
        $this->em->flush();
    }
}
