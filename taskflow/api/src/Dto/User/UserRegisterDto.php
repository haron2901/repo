<?php

namespace App\Dto\User;

use Symfony\Component\Validator\Constraints as Assert;

class UserRegisterDto
{
    #[Assert\NotBlank]
    #[Assert\Email]
    public string $email;

    #[Assert\NotBlank]
    #[Assert\Length(min: 6)]
    public string $password;

    public function __construct(array $data = [])
    {
        $this->email = $data['email'] ?? '';
        $this->password = $data['password'] ?? '';
    }
}
