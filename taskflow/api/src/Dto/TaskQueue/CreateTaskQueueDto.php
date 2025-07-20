<?php

namespace App\Dto\TaskQueue;

use Symfony\Component\Validator\Constraints as Assert;

class CreateTaskQueueDto
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $name;
}
