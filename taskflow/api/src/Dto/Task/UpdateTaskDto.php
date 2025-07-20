<?php

namespace App\Dto\Task;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateTaskDto
{
    #[Assert\Length(max: 255)]
    public ?string $title = null;

    #[Assert\Type('string')]
    public ?string $description = null;

    #[Assert\Choice(choices: ['new', 'in_progress', 'done', 'cancelled'])]
    public ?string $status = null;
    public ?int $assigneeId = null;

    #[Assert\Date]
    public ?string $deadline = null;
}
