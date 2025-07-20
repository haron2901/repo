<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function register(string $email, string $plainPassword): true
    {
        if ($this->userRepository->findOneByEmail($email)) {
            throw new \DomainException('User with this email already exists.');
        }

        $user = new User();
        $user->setEmail($email);
        $hashed = $this->passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($hashed);
        $user->setRoles(['ROLE_USER']);

        $this->userRepository->save($user, true);

        return true;
    }
}
