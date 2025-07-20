<?php

namespace App\Service;

use App\Dto\TaskQueue\CreateTaskQueueDto;
use App\Entity\TaskQueue;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class TaskQueueService
{
    public function __construct(private EntityManagerInterface $em) {}

    public function create(CreateTaskQueueDto $dto, User $owner): TaskQueue
    {
        $queue = new TaskQueue();
        $queue->setName($dto->name);
        $queue->setOwner($owner);

        $this->em->persist($queue);
        $this->em->flush();

        return $queue;
    }

    public function delete(TaskQueue $queue): void
    {
        $this->em->remove($queue);
        $this->em->flush();
    }

    // ... update, list и т.д.
}
