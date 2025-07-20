<?php


namespace App\Dto\Task;

use Symfony\Component\Validator\Constraints as Assert;

class TaskCreateDto
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $title;

    #[Assert\Type('string')]
    public ?string $description = null;

    #[Assert\NotBlank]
    public int $queueId;

    #[Assert\Choice(choices: ['new', 'in_progress', 'done', 'cancelled'])]
    public string $status = 'new';
    public ?int $assigneeId = null;

    #[Assert\Date]
    public ?string $deadline = null;
}
